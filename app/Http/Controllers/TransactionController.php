<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\History;
use App\Models\User; // Import User
use App\Models\Audit;
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
        $isSearchItemMode = false;
        $transactions = null;
        $transactionDetails = null;

        // Ambil parameter sortir (Default: Date Descending)
        $sort = $request->input('sort', 'date-desc');

        // 1. LOGIKA PENCARIAN (Keyword Terisi)
        if ($request->filled('search')) {
            $search = $request->search;

            // SKENARIO A: Cari Nama Barang Dulu (PRIORITAS UTAMA)
            $detailQuery = TransactionDetail::with(['transaction.user', 'item'])
                ->whereHas('item', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('code', 'like', '%' . $search . '%'); // Tambahkan cari kode barang juga biar makin mantap
                });
            
            // Terapkan Filter Tambahan ke Induk Transaksi agar pengecekan exists() akurat
            $detailQuery->whereHas('transaction', function($q) use ($request) {
                if ($request->filled('type')) $q->where('type', $request->type);
                if ($request->filled('market')) $q->where('market', $request->market);
                if ($request->filled('user_id')) $q->where('user_id', $request->user_id);
                if ($request->filled('date_start') && $request->filled('date_end')) {
                    $q->whereBetween('transaction_date', [$request->date_start, $request->date_end]);
                }
            });

            // Jika ditemukan Barang yang cocok...
            if ($detailQuery->exists()) {
                // MODE DETAIL (BARANG)
                
                // Sortir Mode Detail
                if ($sort == 'date-desc') $detailQuery->orderBy('created_at', 'desc');
                if ($sort == 'date-asc') $detailQuery->orderBy('created_at', 'asc');
                if ($sort == 'updated-desc') $detailQuery->orderBy('updated_at', 'desc');
                if ($sort == 'updated-asc') $detailQuery->orderBy('updated_at', 'asc');
                // Sortir Invoice di mode barang agak tricky, kita skip atau default ke date dulu

                $transactionDetails = $detailQuery->paginate(15)->withQueryString();
                $isSearchItemMode = true;
            } 
            
            // SKENARIO B: Jika Barang Tidak Ketemu, Cari Invoice Code
            else {
                $trxQuery = Transaction::with('user')
                    ->where('invoice_code', 'like', '%' . $search . '%');
                
                // MODE NORMAL (INVOICE)
                $transactions = $this->applyFilters($trxQuery, $request, $sort)->paginate(10)->withQueryString();
            }
        } 
        
        // 2. LOGIKA TANPA PENCARIAN (Hanya Filter / Kosong)
        else {
            $query = Transaction::with('user');
            $transactions = $this->applyFilters($query, $request, $sort)->paginate(10)->withQueryString();
        }

        // Data Pendukung untuk Dropdown Filter
        $users = User::all();
        $markets = Transaction::select('market')->distinct()->whereNotNull('market')->pluck('market');

        return view('transactions.index', compact('transactions', 'transactionDetails', 'isSearchItemMode', 'users', 'markets'));
    }

    // Helper untuk filter standar (biar rapi)
    private function applyFilters($query, $request, $sort = null)
    {
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('market')) $query->where('market', $request->market);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        
        if ($request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('transaction_date', [$request->date_start, $request->date_end]);
        }

        // Sortir
        $sort = $sort ?? $request->input('sort', 'newest');
        if ($sort == 'oldest') {
            $query->orderBy('transaction_date', 'asc')->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('transaction_date', 'desc')->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function create()
    {
        $items = Item::orderBy('name')->get();
        $warehouses = \App\Models\Warehouse::all(); // <--- Ambil Data Gudang
        
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
            // 'warehouse_id' => 'required|exists:warehouses,id',
            'cart_items' => 'required|json',
            'market' => $request->type == 'out' ? 'required|string' : 'nullable',
        ]);

        $cartItems = json_decode($request->cart_items, true);
        if (empty($cartItems)) return back()->withErrors(['error' => 'Keranjang belanja kosong!']);

        try {
            DB::transaction(function () use ($request, $cartItems) {
                $grandTotal = 0;
                foreach ($cartItems as $item) $grandTotal += $item['subtotal'];

                $transaction = Transaction::create([
                    'invoice_code' => $request->invoice_code,
                    'user_id' => Auth::id(),
                    // 'warehouse_id' => $request->warehouse_id,
                    'type' => $request->type,
                    'market' => $request->type == 'out' ? $request->market : null, // <--- Market hanya untuk OUT
                    'transaction_date' => $request->transaction_date,
                    'grand_total' => $grandTotal,
                    'description' => $request->description,
                ]);

                foreach ($cartItems as $cart) {
                    $dbItem = Item::find($cart['id']);
                    if (!$dbItem) continue;

                    $finalPrice = ($cart['price'] > 0) ? $cart['price'] : ($request->type == 'in' ? $dbItem->buy_price : $dbItem->sell_price);

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'item_id' => $cart['id'],
                        'quantity' => $cart['qty'],
                        'price' => $finalPrice,
                        'subtotal' => $cart['qty'] * $finalPrice,
                        'buy_price_snapshot' => ($request->type == 'in') ? $finalPrice : $dbItem->buy_price,
                        'sell_price_snapshot' => ($request->type == 'out') ? $finalPrice : $dbItem->sell_price,
                    ]);

                    if ($request->type === 'in') {
                        request()->merge(['history_reason' => "Transaksi Masuk " . $transaction->invoice_code]);
                        
                        // Update Stok Global
                        $updateData = ['stock' => $dbItem->stock + $cart['qty']];
                        if ($cart['price'] > 0 && $cart['price'] != $dbItem->buy_price) $updateData['buy_price'] = $cart['price'];
                        
                        // SINKRONISASI GUDANG (Pivot)
                        // Jika barang masuk ke gudang ini, pastikan relasinya tercatat di pivot
                        // syncWithoutDetaching = Tambahkan gudang ini ke daftar lokasi barang (kalau belum ada)
                        // $dbItem->warehouses()->syncWithoutDetaching([$request->warehouse_id]);

                        $dbItem->update($updateData);

                    } else {
                        request()->merge(['history_reason' => "Transaksi Keluar " . $transaction->invoice_code]);
                        if ($dbItem->stock >= $cart['qty']) {
                            $dbItem->stock = $dbItem->stock - $cart['qty'];
                            $dbItem->save();
                        } else {
                            throw new \Exception("Stok barang {$dbItem->name} tidak cukup! Sisa: {$dbItem->stock}");
                        }
                    }
                }

                // Create an audit entry for the transaction (summary)
                Audit::create([
                    'actor_id' => Auth::id(),
                    'type' => 'transaction.create',
                    'reference_id' => $transaction->id,
                    'payload' => json_encode([
                        'invoice_code' => $transaction->invoice_code,
                        'type' => $transaction->type,
                        'grand_total' => $transaction->grand_total,
                        'items_count' => count($cartItems),
                    ]),
                    'reason' => 'Buat Transaksi ' . $transaction->invoice_code,
                ]);
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
                // Set a reason so ItemObserver will store per-item history
                request()->merge(['history_reason' => 'Hapus Transaksi ' . $transaction->invoice_code]);

                // Kembalikan Stok Barang Sebelum Hapus
                foreach ($transaction->details as $detail) {
                    $item = Item::find($detail->item_id);
                    if (!$item) continue;

                    if ($transaction->type === 'in') {
                        // Kalau tadi IN (nambah stok), sekarang dihapus berarti stok harus DIKURANGI
                        $item->stock = max(0, $item->stock - $detail->quantity);
                        $item->save();
                    } else {
                        // Kalau tadi OUT (kurang stok), sekarang dihapus berarti stok harus DIKEMBALIKAN
                        $item->stock = $item->stock + $detail->quantity;
                        $item->save();
                    }
                }

                // Create audit record about deletion
                Audit::create([
                    'actor_id' => auth()->id(),
                    'type' => 'transaction.delete',
                    'reference_id' => $transaction->id,
                    'payload' => json_encode([
                        'invoice_code' => $transaction->invoice_code,
                        'type' => $transaction->type,
                        'items_count' => $transaction->details->count(),
                    ]),
                    'reason' => 'Hapus Transaksi ' . $transaction->invoice_code,
                ]);

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

        //Ambil data lama & format tanggalnya secara manual agar tidak jadi UTC
        $old = [
            'market' => $transaction->market,
            // Cek apakah transaction_date berupa objek Carbon, jika ya format, jika tidak pakai langsung
            'transaction_date' => $transaction->transaction_date instanceof \Carbon\Carbon 
                ? $transaction->transaction_date->format('Y-m-d') 
                : $transaction->transaction_date,
            'description' => $transaction->description,
        ];

        $transaction->update([
            'market' => $request->market,
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
        ]);

        //Siapkan data baru dengan format tanggal yang dipaksa string
        $newData = [
            'market' => $transaction->market,
            'transaction_date' => \Carbon\Carbon::parse($request->transaction_date)->format('Y-m-d'),
            'description' => $transaction->description,
        ];

        // Audit: record what changed
        Audit::create([
            'actor_id' => auth()->id(),
            'type' => 'transaction.update',
            'reference_id' => $transaction->id,
            'payload' => json_encode([
                'old' => $old,
                'new' => $newData, // Gunakan variabel array yang sudah diformat
            ]),
            'reason' => 'Edit Transaksi ' . $transaction->invoice_code,
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