<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'AKSES DITOLAK: Laporan Keuangan hanya untuk Admin.');
        }

        $data = $this->getReportData($request);
        return view('reports.index', $data);
    }

    public function exportPdf(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $data = $this->getReportData($request);
        $pdf = Pdf::loadView('exports.report_pdf', $data);
        return $pdf->download('laporan-keuangan.pdf');
    }

    public function exportExcel(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        // Kirim data yang sudah dihitung ke Class Export
        $data = $this->getReportData($request);
        return Excel::download(new ReportExport($data), 'laporan-keuangan.xlsx');
    }

    /**
     * Logika Inti Perhitungan Laporan
     */
    private function getReportData(Request $request)
    {
        $startDate = $request->date_start ?? date('Y-m-01');
        $endDate = $request->date_end ?? date('Y-m-t');

        // 1. Data Transaksi Detail (Join untuk performa)
        $details = TransactionDetail::with(['transaction', 'item'])
            ->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            })->get();

        // 2. Hitung Keuangan (Flow)
        $salesDetails = $details->where('transaction.type', 'out');
        $purchaseDetails = $details->where('transaction.type', 'in');

        $omzet = $salesDetails->sum('subtotal');
        $totalPurchase = $purchaseDetails->sum('subtotal'); // Total belanja stok baru

        // HPP (Hanya dari barang terjual)
        $hpp = $salesDetails->sum(fn($d) => $d->quantity * $d->buy_price_snapshot);
        $profit = $omzet - $hpp;

        // 3. Statistik Tambahan (Detail)
        $totalSold = $salesDetails->sum('quantity');
        $totalPurchased = $purchaseDetails->sum('quantity');
        $trxCount = $details->pluck('transaction_id')->unique()->count();

        // Rincian Barang Terlaris (Top 5)
        $topItems = $salesDetails->groupBy('item_id')->map(function ($rows) {
            return [
                'name' => $rows->first()->item->name ?? 'Item Dihapus',
                'qty' => $rows->sum('quantity'),
                'total' => $rows->sum('subtotal'),
            ];
        })->sortByDesc('qty')->take(5);

        // 4. Valuasi Aset (Stock Snapshot) - Tetap pakai logika Backtracking kemarin
        $valuationDate = $endDate;
        $assetValue = 0;
        $totalStockRemaining = 0;
        $items = Item::all();
        $futureTrxDetails = TransactionDetail::whereHas('transaction', fn($q) => 
            $q->whereDate('transaction_date', '>', $valuationDate)
        )->get()->groupBy('item_id');

        foreach ($items as $item) {
            $stockAtDate = $item->stock;
            if (isset($futureTrxDetails[$item->id])) {
                foreach ($futureTrxDetails[$item->id] as $detail) {
                    if ($detail->transaction->type == 'in') $stockAtDate -= $detail->quantity;
                    else $stockAtDate += $detail->quantity;
                }
            }
            $stockAtDate = max(0, $stockAtDate);
            $assetValue += $stockAtDate * $item->buy_price;
            $totalStockRemaining += $stockAtDate;
        }

        return compact(
            'startDate', 'endDate', 
            'omzet', 'hpp', 'profit', 'totalPurchase',
            'totalSold', 'totalPurchased', 'trxCount', 
            'assetValue', 'valuationDate', 'topItems',
            'totalStockRemaining'
        );
    }
}