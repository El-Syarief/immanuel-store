<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Menampilkan daftar riwayat aktivitas barang
     */
    public function index(Request $request)
    {
        $query = History::with(['item', 'user']);

        // 1. Filter Barang
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        // 2. Filter User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // 3. Filter Rentang Waktu (PERUBAHAN DISINI)
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $histories = $query->latest()->paginate(15)->withQueryString();

        $items = Item::orderBy('name')->get();
        $users = User::all();

        return view('histories.index', compact('histories', 'items', 'users'));
    }
}