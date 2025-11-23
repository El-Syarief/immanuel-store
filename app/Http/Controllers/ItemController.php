<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Menampilkan daftar barang (Index)
     */
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

        // 4. Filter Market (BARU)
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

        // Data untuk Dropdown
        $creators = User::whereHas('createdItems')->get();
        $criterias = Item::select('criteria')->distinct()->whereNotNull('criteria')->pluck('criteria');
        
        // Ambil data Market unik (BARU)
        $markets = Item::select('market')->distinct()->whereNotNull('market')->pluck('market');

        return view('items.index', compact('items', 'creators', 'criterias', 'markets'));
    }

    /**
     * Menampilkan form tambah barang (Create)
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Menyimpan barang baru ke database (Store)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'code' => 'required|unique:items,code|max:255',
            'name' => 'required|string|max:255',
            'market' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
        ]);

        // 2. Simpan ke Database
        Item::create([
            'code' => $request->code,
            'name' => $request->name,
            'market' => $request->market,
            'criteria' => $request->criteria, // Opsional
            'stock' => $request->stock,
            'buy_price' => $request->buy_price,
            'sell_price' => $request->sell_price,
            'description' => $request->description, // Opsional
            'created_by' => auth()->id(), // Simpan ID admin yang login
        ]);

        // 3. Kembali ke halaman index dengan pesan sukses
        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan!');
    }
    
    /**
     * Menampilkan form edit barang
     */
    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    /**
     * Mengupdate data barang di database
     */
    public function update(Request $request, Item $item)
    {
        // Validasi Input
        $request->validate([
            // Code harus unik, TAPI abaikan (ignore) untuk barang ini sendiri agar tidak error "Code sudah ada"
            'code' => 'required|max:255|unique:items,code,' . $item->id,
            'name' => 'required|string|max:255',
            'market' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
        ]);

        // Simpan Perubahan
        $item->update([
            'code' => $request->code,
            'name' => $request->name,
            'market' => $request->market,
            'criteria' => $request->criteria,
            'stock' => $request->stock,
            'buy_price' => $request->buy_price,
            'sell_price' => $request->sell_price,
            'description' => $request->description,
        ]);

        return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui!');
    }

    /**
     * Menghapus barang (Soft Delete)
     */
    public function destroy(Item $item)
    {
        $item->delete(); // Soft delete karena di Model sudah pakai Trait SoftDeletes
        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus!');
    }
}