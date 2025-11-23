<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\ItemPriceHistory;
use App\Models\User; // Import User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user');

        // 1. Filter Type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 2. Filter Market
        if ($request->filled('market')) {
            $query->where('market', 'like', '%' . $request->market . '%');
        }

        // 3. Filter Kasir/User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // 4. Filter Tanggal
        if ($request->filled('date_start')) {
            $query->whereDate('transaction_date', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('transaction_date', '<=', $request->date_end);
        }

        // 5. Pencarian No Invoice
        if ($request->filled('search')) {
            $query->where('invoice_code', 'like', '%' . $request->search . '%');
        }

        // 6. Sortir
        $sortField = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $query->orderBy($sortField, $sortDir);

        $transactions = $query->paginate(10)->withQueryString();
        $users = User::all(); // Untuk dropdown filter kasir

        return view('transactions.index', compact('transactions', 'users'));
    }

    public function create()
    {
        $items = Item::orderBy('name')->get();
        
        // Kita hanya kirim Sequence Number (Urutan) ke View
        // Nanti Javascript yang akan merangkai jadi "INV-IN-..." atau "INV-OUT-..."
        $todayPrefix = date('Ymd');
        $lastTransaction = Transaction::whereDate('created_at', today())->latest()->first();
        $nextSeq = $lastTransaction ? ((int)substr($lastTransaction->invoice_code, -4) + 1) : 1;
        $seqString = str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

        return view('transactions.create', compact('items', 'todayPrefix', 'seqString'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_code' => 'required|unique:transactions,invoice_code',
            'transaction_date' => 'required|date',
            'type' => 'required|in:in,out',
            'cart_items' => 'required|json',
        ]);

        $cartItems = json_decode($request->cart_items, true);

        if (empty($cartItems)) {
            return back()->withErrors(['error' => 'Keranjang belanja kosong!']);
        }

        try {
            DB::transaction(function () use ($request, $cartItems) {
                
                $grandTotal = 0;
                foreach ($cartItems as $item) {
                    $grandTotal += $item['subtotal'];
                }

                // Format Invoice Ulang (Backend Validation) agar konsisten
                // Format: INV-{TYPE}-{DATE}-{SEQ} -> INV-OUT-20251123-0001
                // Kita percaya input invoice_code dari frontend, tapi idealnya digenerate ulang di sini untuk keamanan.
                // Untuk sekarang kita pakai input dari request saja agar sesuai tampilan.

                $transaction = Transaction::create([
                    'invoice_code' => $request->invoice_code,
                    'user_id' => Auth::id(),
                    'type' => $request->type,
                    'market' => $request->market,
                    'transaction_date' => $request->transaction_date,
                    'grand_total' => $grandTotal,
                    'description' => $request->description,
                ]);

                foreach ($cartItems as $cart) {
                    $dbItem = Item::find($cart['id']);
                    if (!$dbItem) continue;

                    $finalPrice = ($cart['price'] > 0) 
                        ? $cart['price'] 
                        : ($request->type == 'in' ? $dbItem->buy_price : $dbItem->sell_price);

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'item_id' => $cart['id'],
                        'quantity' => $cart['qty'],

                        'price' => $finalPrice, // <--- Pakai harga yang sudah diamankan
                        'subtotal' => $cart['qty'] * $finalPrice, // Hitung ulang subtotal biar akurat

                        // 'price' => $cart['price'],
                        // 'subtotal' => $cart['subtotal'],

                        'buy_price_snapshot' => $dbItem->buy_price,
                        'sell_price_snapshot' => $dbItem->sell_price,
                    ]);

                    // --- LOGIKA STOK (PERBAIKAN BUG) ---
                    if ($request->type === 'in') {
                        // IN: Tambah Stok
                        $dbItem->increment('stock', $cart['qty']);

                        // Update Harga/Market jika perlu
                        $updateData = [];
                        if ($cart['price'] > 0 && $cart['price'] != $dbItem->buy_price) {
                            $updateData['buy_price'] = $cart['price'];
                        }
                        if ($request->filled('market') && $request->market !== $dbItem->market) {
                            $updateData['market'] = $request->market;
                        }

                        if (!empty($updateData)) {
                            request()->merge(['history_reason' => "Transaksi Masuk " . $transaction->invoice_code]);
                            $dbItem->update($updateData);
                        }

                    } else {
                        // OUT: Kurangi Stok
                        // Gunakan query builder langsung agar lebih pasti
                        if ($dbItem->stock >= $cart['qty']) {
                            Item::where('id', $cart['id'])->decrement('stock', $cart['qty']);
                        } else {
                            throw new \Exception("Stok barang {$dbItem->name} tidak cukup! Sisa: {$dbItem->stock}");
                        }
                    }
                }
            });

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('details.item', 'user');
        return view('transactions.show', compact('transaction'));
    }

    public function destroy(Transaction $transaction)
    {
        // HANYA ADMIN
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang boleh menghapus transaksi.');
        }

        try {
            DB::transaction(function () use ($transaction) {
                // Kembalikan Stok Barang Sebelum Hapus
                foreach ($transaction->details as $detail) {
                    if ($transaction->type === 'in') {
                        // Kalau tadi IN (nambah stok), sekarang dihapus berarti stok harus DIKURANGI
                        Item::where('id', $detail->item_id)->decrement('stock', $detail->quantity);
                    } else {
                        // Kalau tadi OUT (kurang stok), sekarang dihapus berarti stok harus DIKEMBALIKAN
                        Item::where('id', $detail->item_id)->increment('stock', $detail->quantity);
                    }
                }
                
                $transaction->delete(); // Hapus Transaksi
            });

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus & stok dikembalikan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    public function edit(Transaction $transaction)
    {
        // Cegah edit jika bukan admin (opsional, bisa diatur di route juga)
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
        return view('transactions.edit', compact('transaction'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'market' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $transaction->update([
            'market' => $request->market,
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Data transaksi berhasil diperbarui.');
    }
}