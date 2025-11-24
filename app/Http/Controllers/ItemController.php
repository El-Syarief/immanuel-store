<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'AKSES DITOLAK: Hanya Admin yang boleh mengelola data barang.');
        }
    }

    public function index(Request $request)
    {
        $query = Item::query()->with('creator');

        // 1. Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        // 2. Filter Kriteria
        if ($request->filled('criteria')) {
            $query->where('criteria', $request->criteria);
        }

        // 3. Filter Pembuat
        if ($request->filled('creator_id')) {
            $query->where('created_by', $request->creator_id);
        }

        // 4. Filter Market
        if ($request->filled('market')) {
            $query->where('market', $request->market);
        }

        // 5. Sortir
        $sortParam = $request->input('sort_by', 'created_at-desc'); 
        $sortParts = explode('-', $sortParam);
        $field = $sortParts[0]; 
        $direction = $sortParts[1] ?? 'desc';

        $allowedSorts = ['name', 'created_at', 'updated_at', 'stock', 'price'];
        if (in_array($field, $allowedSorts)) {
            $query->orderBy($field, $direction);
        }

        $items = $query->paginate(10)->withQueryString();

        $creators = User::whereHas('createdItems')->get();
        $criterias = Item::select('criteria')->distinct()->whereNotNull('criteria')->pluck('criteria');
        $markets = Item::select('market')->distinct()->whereNotNull('market')->pluck('market');

        return view('items.index', compact('items', 'creators', 'criterias', 'markets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeAdmin();
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'code' => 'required|unique:items,code|max:255',
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'market' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Simpan Item Baru
            // Kita inject 'history_reason' agar Observer mencatat alasan yang spesifik
            request()->merge(['history_reason' => 'Input Barang Baru (Initial Stock)']);
            
            $item = Item::create([
                'code' => $request->code,
                'name' => $request->name,
                'criteria' => $request->criteria,
                'stock' => $request->stock,
                'buy_price' => $request->buy_price,
                'sell_price' => $request->sell_price,
                'market' => $request->market,
                'description' => $request->description,
                'created_by' => auth()->id(),
            ]);

            // 2. SINKRONISASI TRANSAKSI (Jika ada stok awal)
            if ($item->stock > 0) {
                // Buat Transaksi Otomatis (Tipe IN)
                $trx = Transaction::create([
                    'invoice_code' => 'SYS-INIT-' . time() . rand(100,999), // Kode Unik System
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'market' => $item->market,
                    'transaction_date' => now(),
                    'grand_total' => $item->stock * $item->buy_price, // Nilai Aset
                    'description' => 'Stok Awal Otomatis saat Input Barang Baru: ' . $item->name,
                ]);

                TransactionDetail::create([
                    'transaction_id' => $trx->id,
                    'item_id' => $item->id,
                    'quantity' => $item->stock,
                    'price' => $item->buy_price, // Gunakan Harga Modal (Bukan Jual)
                    'subtotal' => $item->stock * $item->buy_price,
                    'buy_price_snapshot' => $item->buy_price,
                    'sell_price_snapshot' => $item->sell_price,
                ]);
            }
        });

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan & tercatat di transaksi!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $this->authorizeAdmin();
        return view('items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $this->authorizeAdmin();

        $request->validate([
            'code' => 'required|max:255|unique:items,code,' . $item->id,
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'market' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $item) {
            $oldStock = $item->stock;
            $newStock = $request->stock;
            $diff = $newStock - $oldStock;

            // 1. Update Item
            // Inject reason agar Observer mencatat ini edit manual
            request()->merge(['history_reason' => 'Edit Manual via Menu Items']);
            
            $item->update([
                'code' => $request->code,
                'name' => $request->name,
                'criteria' => $request->criteria,
                'stock' => $newStock,
                'buy_price' => $request->buy_price,
                'sell_price' => $request->sell_price,
                'market' => $request->market,
                'description' => $request->description,
            ]);

            // 2. SINKRONISASI TRANSAKSI (Jika Stok Berubah)
            if ($diff != 0) {
                $type = $diff > 0 ? 'in' : 'out';
                $qty = abs($diff);
                
                // Kita gunakan Harga Modal (buy_price) untuk nilai koreksi
                // Agar tidak dianggap Profit/Untung, melainkan Penyesuaian Aset saja.
                $price = $item->buy_price; 
                $subtotal = $qty * $price;

                $trx = Transaction::create([
                    'invoice_code' => 'SYS-ADJ-' . time() . rand(100,999), // Kode System Adjustment
                    'user_id' => auth()->id(),
                    'type' => $type,
                    'market' => $item->market, 
                    'transaction_date' => now(),
                    'grand_total' => $subtotal,
                    'description' => 'Koreksi Stok Manual (Edit Item): ' . ($diff > 0 ? "Penambahan $qty" : "Pengurangan $qty"),
                ]);

                TransactionDetail::create([
                    'transaction_id' => $trx->id,
                    'item_id' => $item->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'buy_price_snapshot' => $item->buy_price,
                    'sell_price_snapshot' => $item->sell_price,
                ]);
            }
        });

        return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui & stok disinkronkan!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $this->authorizeAdmin();

        $item->delete();
        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus!');
    }

    // --- FITUR EXPORT ---
    
    public function exportExcel(Request $request)
    {
        $this->authorizeAdmin();

        return Excel::download(new ItemsExport($request), 'data-barang.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $this->authorizeAdmin();

        $query = Item::query()->with('creator');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }
        if ($request->filled('criteria')) $query->where('criteria', $request->criteria);
        if ($request->filled('creator_id')) $query->where('created_by', $request->creator_id);
        if ($request->filled('market')) $query->where('market', $request->market);
        
        $items = $query->get();
        $pdf = Pdf::loadView('exports.items_pdf', compact('items'));
        return $pdf->download('laporan-barang.pdf');
    }
}