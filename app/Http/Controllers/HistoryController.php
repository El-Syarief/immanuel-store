<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Item;
use App\Models\User;
use App\Models\Audit;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Menampilkan daftar riwayat aktivitas barang
     */
    public function index(Request $request)
    {
        // Eager load item -> warehouses to avoid N+1 when showing detail
        $query = History::with(['item.warehouses', 'user']);

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

        $historiesList = $query->latest()->get();

        // Build audits query (system-level actions)
        $auditsQuery = Audit::with('actor');
        if ($request->filled('user_id')) {
            $auditsQuery->where('actor_id', $request->user_id);
        }
        if ($request->filled('date_start')) {
            $auditsQuery->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $auditsQuery->whereDate('created_at', '<=', $request->date_end);
        }
        $auditsList = $auditsQuery->latest()->get();

        // Normalize into unified entries collection
        $historiesNormalized = $historiesList->map(function($h) {
            return (object)[
                'kind' => 'history',
                'created_at' => $h->created_at,
                'data' => $h,
            ];
        });

        $auditsNormalized = $auditsList->map(function($a) {
            return (object)[
                'kind' => 'audit',
                'created_at' => $a->created_at,
                'data' => $a,
            ];
        });

        $merged = $historiesNormalized->concat($auditsNormalized)->sortByDesc('created_at')->values();

        // Manual pagination
        $perPage = 15;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $itemsForPage = $merged->slice(($page - 1) * $perPage, $perPage)->values();
        $paginator = new LengthAwarePaginator($itemsForPage, $merged->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => request()->query(),
        ]);

        $items = Item::orderBy('name')->get();
        $users = User::all();

        return view('histories.index', ['histories' => $paginator, 'items' => $items, 'users' => $users]);
    }
}