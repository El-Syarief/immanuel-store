<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Warehouse;
use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ItemController extends Controller
{
    /**
     * Helper: Proses input gudang (ID atau Nama Baru)
     * Mengembalikan array ID gudang yang valid.
     */
    private function processWarehouses($inputs)
    {
        if (!$inputs) return [];

        $ids = [];
        foreach ($inputs as $input) {
            if (is_numeric($input)) {
                // Jika angka, berarti ID gudang yang sudah ada
                $ids[] = $input;
            } else {
                // Jika string, berarti nama gudang baru -> Buat dulu!
                // firstOrCreate mencegah duplikat jika user mengetik nama yang sama
                $new = Warehouse::firstOrCreate(['name' => $input]);
                $ids[] = $new->id;
            }
        }
        return $ids;
    }

    private function authorizeAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'AKSES DITOLAK: Hanya Admin yang boleh mengelola data barang.');
        }
    }

    public function index(Request $request)
    {
        $query = Item::query()->with(['creator', 'warehouses']);

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

        // 4. Filter Gudang
        if ($request->filled('warehouse_id')) {
            $query->whereHas('warehouses', function($q) use ($request) {
                $q->where('warehouses.id', $request->warehouse_id);
            });
        }

        // 5. Sortir
        $sortParam = $request->input('sort_by', 'created_at-desc'); 
        $sortParts = explode('-', $sortParam);
        $field = $sortParts[0]; 
        $direction = $sortParts[1] ?? 'desc';

        $allowedSorts = ['name', 'created_at', 'updated_at', 'stock', 'price', 'code'];
        if (in_array($field, $allowedSorts)) {
            $query->orderBy($field, $direction);
        }

        $items = $query->paginate(10)->withQueryString();

        $creators = User::whereHas('createdItems')->get();
        $criterias = Item::select('criteria')->distinct()->whereNotNull('criteria')->pluck('criteria');
        $warehouses = Warehouse::all(); 

        return view('items.index', compact('items', 'creators', 'criterias', 'warehouses'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        $warehouses = Warehouse::all();
        return view('items.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'code' => 'required|unique:items,code|max:255',
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            
            // PERBAIKAN VALIDASI:
            // Hapus 'exists' agar user bisa input gudang baru (string)
            'warehouses' => 'nullable|array',
            'warehouses.*' => 'required', 
        ]);

        DB::transaction(function () use ($request) {
            request()->merge(['history_reason' => 'Input Barang Baru (Initial Stock)']);
            
            $item = Item::create([
                'code' => $request->code,
                'name' => $request->name,
                'criteria' => $request->criteria,
                'stock' => $request->stock,
                'buy_price' => $request->buy_price,
                'sell_price' => $request->sell_price,
                'description' => $request->description,
                'created_by' => auth()->id(),
            ]);

            // PROSES GUDANG (Pakai Helper yang baru dibuat)
            if ($request->has('warehouses')) {
                $warehouseIds = $this->processWarehouses($request->warehouses);
                $item->warehouses()->attach($warehouseIds);
            }

            // SINKRONISASI TRANSAKSI (Jika ada stok awal)
            if ($item->stock > 0) {
                // Ambil gudang pertama untuk lokasi stok awal
                $warehouseIds = $this->processWarehouses($request->warehouses ?? []);
                $primaryWarehouseId = $warehouseIds[0] ?? null;

                $trx = Transaction::create([
                    'invoice_code' => 'SYS-INIT-' . time() . rand(100,999),
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'warehouse_id' => $primaryWarehouseId, 
                    'market' => 'System Inventory (Awal)', // PERBAIKAN BUG: Jangan pakai $item->market (sudah dihapus)
                    'transaction_date' => now(),
                    'grand_total' => $item->stock * $item->buy_price,
                    'description' => 'Stok Awal Otomatis saat Input Barang Baru: ' . $item->name,
                ]);

                TransactionDetail::create([
                    'transaction_id' => $trx->id,
                    'item_id' => $item->id,
                    'quantity' => $item->stock,
                    'price' => $item->buy_price,
                    'subtotal' => $item->stock * $item->buy_price,
                    'buy_price_snapshot' => $item->buy_price,
                    'sell_price_snapshot' => $item->sell_price,
                ]);
            }
        });

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    public function show(Item $item)
    {
        // Muat relasi agar kita bisa menampilkan nama pembuat & gudang
        $item->load(['creator', 'warehouses']);
        
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $this->authorizeAdmin();
        $warehouses = Warehouse::all();
        $item->load('warehouses');
        return view('items.edit', compact('item', 'warehouses'));
    }

    public function update(Request $request, Item $item)
    {
        $this->authorizeAdmin();

        $request->validate([
            'code' => 'required|max:255|unique:items,code,' . $item->id,
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'warehouses' => 'nullable|array',
            'warehouses.*' => 'required',
        ]);

        DB::transaction(function () use ($request, $item) {
            $oldData = $item->fresh(['warehouses']);
            $oldWarehouses = $oldData->warehouses->pluck('name')->toArray();
            $oldAttributes = $oldData->only(['code', 'name', 'criteria', 'description']);

            $oldStock = $item->stock;
            $newStock = $request->stock;
            $diff = $newStock - $oldStock;

            request()->merge(['history_reason' => 'Edit Manual via Menu Items']);
            
            $item->update([
                'code' => $request->code,
                'name' => $request->name,
                'criteria' => $request->criteria,
                'stock' => $newStock,
                'buy_price' => $request->buy_price,
                'sell_price' => $request->sell_price,
                'description' => $request->description,
            ]);

            // UPDATE GUDANG (Pakai Helper)
            if ($request->has('warehouses')) {
                $warehouseIds = $this->processWarehouses($request->warehouses);
                $item->warehouses()->sync($warehouseIds);
            } else {
                $item->warehouses()->detach();
            }

            // AUDIT LOG MANUAL (UNTUK DATA ADMINISTRATIF)
            // Kita ambil data baru setelah update
            $newItem = $item->fresh(['warehouses']);
            $newWarehouses = $newItem->warehouses->pluck('name')->toArray();
            $newAttributes = $newItem->only(['code', 'name', 'criteria', 'description']);

            $changes = [];

            // Bandingkan atribut dasar (Code, Name, Criteria, Description)
            foreach ($oldAttributes as $key => $value) {
                if ($newAttributes[$key] != $value) {
                    $changes[$key] = [
                        'old' => $value ?? '-', 
                        'new' => $newAttributes[$key] ?? '-'
                    ];
                }
            }

            // Bandingkan gudang (Array comparison)
            // Jika susunan gudang berubah, catat.
            sort($oldWarehouses); // sort agar urutan tidak mempengaruhi
            sort($newWarehouses);
            if ($oldWarehouses !== $newWarehouses) {
                $changes['warehouses'] = [
                    'old' => implode(', ', $oldWarehouses),
                    'new' => implode(', ', $newWarehouses)
                ];
            }

            // Jika ada perubahan data (selain stok/harga yg dihandle observer), simpan ke Audit
            if (!empty($changes)) {
                Audit::create([
                    'actor_id' => auth()->id(),
                    'type' => 'item.update_info', // Tipe khusus info
                    'reference_id' => $item->id,
                    'payload' => json_encode($changes), // Simpan perubahannya
                    'reason' => 'Update Detail Barang: ' . $item->code . ' - ' . $item->name,
                ]);
            }

            // SINKRONISASI TRANSAKSI KOREKSI
            if ($diff != 0) {
                $type = $diff > 0 ? 'in' : 'out';
                $qty = abs($diff);
                $price = $item->buy_price; 
                $subtotal = $qty * $price;
                $warehouseId = $item->warehouses->first()->id ?? null;

                // Ambil gudang pertama dari relasi terbaru
                $warehouseId = $item->warehouses->first()->id ?? null;

                $trx = Transaction::create([
                    'invoice_code' => 'SYS-ADJ-' . time() . rand(100,999),
                    'user_id' => auth()->id(),
                    'type' => $type,
                    'warehouse_id' => $warehouseId,
                    'market' => 'System Correction',
                    'transaction_date' => now(),
                    'grand_total' => $subtotal,
                    'description' => 'Koreksi Stok Manual: ' . ($diff > 0 ? "+$qty" : "-$qty"),
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

        return redirect()->route('items.index')->with('success', 'Barang diperbarui!');
    }

    public function destroy(Item $item)
    {
        $this->authorizeAdmin();
        // Set a reason so ItemObserver will record this deletion
        request()->merge(['history_reason' => 'Hapus Barang: ' . $item->name]);
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus!');
    }

    public function exportExcel(Request $request)
    {
        $this->authorizeAdmin();
        return Excel::download(new ItemsExport($request), 'data-barang.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $this->authorizeAdmin();
        
        // Update query export agar support filter gudang
        $query = Item::query()->with(['creator', 'warehouses']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }
        if ($request->filled('criteria')) $query->where('criteria', $request->criteria);
        if ($request->filled('creator_id')) $query->where('created_by', $request->creator_id);
        
        if ($request->filled('warehouse_id')) {
            $query->whereHas('warehouses', function($q) use ($request) {
                $q->where('warehouses.id', $request->warehouse_id);
            });
        }
        
        $items = $query->get();
        $pdf = Pdf::loadView('exports.items_pdf', compact('items'));
        return $pdf->download('laporan-barang.pdf');
    }
}