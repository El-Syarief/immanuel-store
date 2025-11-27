<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // --- STATISTIK UMUM (Bisa dilihat Admin & Kasir) ---
        
        // 1. Transaksi Hari Ini (Out)
        $todayTransactions = Transaction::where('type', 'out')
            ->whereDate('transaction_date', today())
            ->count();

        // 2. Omzet Hari Ini
        $todayRevenue = Transaction::where('type', 'out')
            ->whereDate('transaction_date', today())
            ->sum('grand_total');

        // 3. Barang Terjual Hari Ini
        $todayItemsSold = TransactionDetail::whereHas('transaction', function($q) {
            $q->where('type', 'out')->whereDate('transaction_date', today());
        })->sum('quantity');

        // 4. Stok Menipis (Alert) - Barang dengan stok < 5
        $lowStockItems = Item::where('stock', '<', 5)->where('stock', '>=', 0)->limit(5)->get();

        // --- STATISTIK KHUSUS ADMIN (Opsional untuk Grafik) ---
        // Ambil data penjualan 7 hari terakhir untuk grafik
        $salesChart = Transaction::where('type', 'out')
            ->where('transaction_date', '>=', now()->subDays(6))
            ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('sum(grand_total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        return view('dashboard', compact(
            'todayTransactions', 
            'todayRevenue', 
            'todayItemsSold', 
            'lowStockItems', 
            'salesChart'
        ));
    }
}