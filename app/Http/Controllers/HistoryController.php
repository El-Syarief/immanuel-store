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
        // Init Query
        $query = History::with(['item.warehouses', 'user']); // Tabel History (Stok)
        $auditsQuery = Audit::with('actor');                 // Tabel Audit (System Log)

        // 1. FILTER: Item (Barang)
        if ($request->filled('item_id')) {
            // A. Filter History: Mudah, karena ada kolom item_id
            $query->where('item_id', $request->item_id);

            // B. Filter Audit: Agak tricky.
            // Kita hanya ambil Audit yang tipe-nya 'item...' DAN reference_id-nya sama dengan item_id
            // Contoh: 'item.update', 'item.delete'
            $auditsQuery->where(function($q) use ($request) {
                $q->where('type', 'like', 'item.%')
                  ->where('reference_id', $request->item_id);
            });
        }

        // 2. FILTER: Text Search (Kata Kunci / Aktivitas)
        if ($request->filled('search_text')) {
            $keyword = $request->search_text;

            // A. Filter History
            $query->where('reason', 'like', '%' . $keyword . '%');

            // B. Filter Audit
            $auditsQuery->where(function($q) use ($keyword) {
                $q->where('reason', 'like', '%' . $keyword . '%')
                  ->orWhere('type', 'like', '%' . $keyword . '%')
                  ->orWhere('payload', 'like', '%' . $keyword . '%');
            });
        }

        // 3. FILTER: User (Aktor)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
            $auditsQuery->where('actor_id', $request->user_id);
        }

        // 4. FILTER: Tanggal
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
            $auditsQuery->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
            $auditsQuery->whereDate('created_at', '<=', $request->date_end);
        }

        // --- EKSEKUSI & GABUNG ---
        $historiesList = $query->latest()->get();
        $auditsList = $auditsQuery->latest()->get();

        // Normalize History
        $historiesNormalized = $historiesList->map(function($h) {
            return (object)[ 'kind' => 'history', 'created_at' => $h->created_at, 'data' => $h ];
        });

        // Normalize Audit
        $auditsNormalized = $auditsList->map(function($a) {
            return (object)[ 'kind' => 'audit', 'created_at' => $a->created_at, 'data' => $a ];
        });

        // Merge, Sort, & Paginate
        $merged = $historiesNormalized->concat($auditsNormalized)->sortByDesc('created_at')->values();

        $perPage = 15;
        $page = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $itemsForPage = $merged->slice(($page - 1) * $perPage, $perPage)->values();
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForPage, $merged->count(), $perPage, $page, 
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'query' => request()->query()]
        );

        $items = Item::orderBy('name')->get();
        $users = User::all();

        return view('histories.index', ['histories' => $paginator, 'items' => $items, 'users' => $users]);
    }
}