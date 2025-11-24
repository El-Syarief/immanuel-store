<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\History;
use App\Models\User; // Import User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user');

        // 1. Filter Tipe (IN/OUT)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 2. Filter Market (Dropdown)
        if ($request->filled('market')) {
            $query->where('market', $request->market);
        }

        // 3. Filter Kasir
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // 4. Filter Tanggal Spesifik (Pencarian Tanggal)
        if ($request->filled('search_date')) {
            $query->whereDate('transaction_date', $request->search_date);
        }

        // 5. Filter Bulan & Tahun
        if ($request->filled('month')) {
            $query->whereMonth('transaction_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('transaction_date', $request->year);
        }

        // 6. Filter Rentang Waktu (Date Range)
        if ($request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('transaction_date', [$request->date_start, $request->date_end]);
        }

        // 7. Pencarian No Invoice
        if ($request->filled('search')) {
            $query->where('invoice_code', 'like', '%' . $request->search . '%');
        }

        // 8. Sortir (Default: Terbaru)
        $sort = $request->input('sort', 'newest');
        if ($sort == 'oldest') {
            $query->orderBy('transaction_date', 'asc')->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('transaction_date', 'desc')->orderBy('created_at', 'desc');
        }

        $transactions = $query->paginate(10)->withQueryString();
        
        // Data Pendukung untuk Filter
        $users = User::all();
        $markets = Transaction::select('market')->distinct()->whereNotNull('market')->pluck('market');
        
        // Ambil tahun-tahun yang ada di transaksi untuk dropdown tahun
        $years = Transaction::selectRaw('YEAR(transaction_date) as year')->distinct()->pluck('year');

        return view('transactions.index', compact('transactions', 'users', 'markets', 'years'));
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
        if (auth()->user()->role === 'cashier' && $request->type === 'in') {
            abort(403, 'AKSES DITOLAK: Kasir tidak diizinkan melakukan pembelian stok (Transaksi IN).');
        }

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

                        // PERBAIKAN SNAPSHOT:
                        // 1. Snapshot Harga Beli (Modal)
                        // Jika ini transaksi IN (Update Stok), maka modalnya adalah harga baru ($finalPrice).
                        // Jika transaksi OUT, modalnya tetap ambil dari database ($dbItem->buy_price).
                        'buy_price_snapshot' => ($request->type == 'in') ? $finalPrice : $dbItem->buy_price,

                        // 2. Snapshot Harga Jual
                        // Biasanya harga jual standar (label) tidak berubah saat transaksi, jadi tetap ambil dari DB.
                        // Tapi jika kamu ingin snapshotnya mengikuti harga deal juga, ubah $dbItem->sell_price jadi $finalPrice (hanya utk type 'out')
                        // 'sell_price_snapshot' => $finalPrice,
                        'sell_price_snapshot' => ($request->type == 'out') ? $finalPrice : $dbItem->sell_price,
                    ]);

                    // --- LOGIKA STOK (PERBAIKAN DOUBLE HISTORY) ---
                    if ($request->type === 'in') {
                        // IN: Tambah Stok & Update Data
                        
                        // 1. Siapkan alasan history
                        request()->merge(['history_reason' => "Transaksi Masuk " . $transaction->invoice_code]);

                        // 2. Siapkan data yang mau diupdate (Stok PASTI update)
                        $updateData = [
                            'stock' => $dbItem->stock + $cart['qty'], // Hitung stok baru manual di sini
                        ];

                        // 3. Cek apakah Harga Modal berubah?
                        if ($cart['price'] > 0 && $cart['price'] != $dbItem->buy_price) {
                            $updateData['buy_price'] = $cart['price'];
                        }

                        // 4. Cek apakah Market berubah?
                        if ($request->filled('market') && $request->market !== $dbItem->market) {
                            $updateData['market'] = $request->market;
                        }

                        // 5. EKSEKUSI UPDATE (HANYA SEKALI)
                        // Ini akan men-trigger Observer satu kali saja, mencatat perubahan stok & harga sekaligus.
                        $dbItem->update($updateData);

                    } else {
                        // OUT: Kurangi Stok (FIX AGAR TERCATAT HISTORY)
                        
                        // 1. Siapkan alasan
                        request()->merge(['history_reason' => "Transaksi Keluar " . $transaction->invoice_code]);

                        // 2. Cek Stok
                        if ($dbItem->stock >= $cart['qty']) {
                            
                            // 3. Update pakai cara Model (Bukan Query Builder)
                            $dbItem->stock = $dbItem->stock - $cart['qty'];
                            $dbItem->save(); // <--- Fungsi save() ini yang memanggil Observer!
                            
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

    public function exportExcel(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        return Excel::download(new TransactionsExport($request), 'data-transaksi.xlsx');
    }

    public function exportPdf(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        // $query = Transaction::with('user');
        $query = Transaction::with(['user', 'details.item']);
        
        // Filter Tipe
        if ($request->filled('type')) $query->where('type', $request->type);
        // Filter Market
        if ($request->filled('market')) $query->where('market', $request->market);
        // Filter Tanggal
        if ($request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('transaction_date', [$request->date_start, $request->date_end]);
        }

        $transactions = $query->latest()->get();
        $pdf = Pdf::loadView('exports.transactions_pdf', compact('transactions'));
        return $pdf->download('laporan-transaksi-detail.pdf');
    }
}