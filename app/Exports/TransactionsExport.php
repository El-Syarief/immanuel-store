<?php

namespace App\Exports;

use App\Models\TransactionDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        // Ambil Detail Transaksi, join dengan Header Transaksi & Item
        $query = TransactionDetail::query()
            ->with(['transaction.user', 'item']);

        // Filter berdasarkan Header Transaksi
        // Kita pakai whereHas untuk memfilter berdasarkan induknya (Transaction)
        $query->whereHas('transaction', function (Builder $q) {
            // Filter Tipe
            if ($this->request->filled('type')) {
                $q->where('type', $this->request->type);
            }
            // Filter Market
            if ($this->request->filled('market')) {
                $q->where('market', $this->request->market);
            }
            // Filter User/Kasir
            if ($this->request->filled('user_id')) {
                $q->where('user_id', $this->request->user_id);
            }
            // Filter Rentang Tanggal
            if ($this->request->filled('date_start') && $this->request->filled('date_end')) {
                $q->whereBetween('transaction_date', [$this->request->date_start, $this->request->date_end]);
            }
        });

        // Urutkan berdasarkan tanggal transaksi terbaru
        return $query->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                     ->orderBy('transactions.transaction_date', 'desc')
                     ->select('transaction_details.*');
    }

    public function headings(): array
    {
        return [
            'No Invoice', 
            'Tipe', 
            'Tanggal', 
            'Market', 
            'Kasir', 
            'Nama Barang', 
            'Harga Transaksi', // Harga Deal (Beli/Jual)
            'Harga Audit',     // Kolom Baru (Modal/Jual)
            'Qty', 
            'Subtotal Barang',
            'Total Invoice',
            'Deskripsi'
        ];
    }

    public function map($detail): array
    {
        // LOGIKA HARGA UTAMA (Deal Price)
        $hargaTransaksi = $detail->price;
        if ($hargaTransaksi <= 0) {
            $hargaTransaksi = ($detail->transaction->type == 'in') 
                ? $detail->buy_price_snapshot 
                : $detail->sell_price_snapshot;
        }

        // LOGIKA HARGA AUDIT (Pembanding)
        // Jika IN (Beli) -> Tampilkan Harga Jual saat itu (untuk cek margin potensial)
        // Jika OUT (Jual) -> Tampilkan Harga Modal saat itu (untuk cek profit)
        $hargaAudit = ($detail->transaction->type == 'in') 
            ? $detail->sell_price_snapshot 
            : $detail->buy_price_snapshot;

        return [
            $detail->transaction->invoice_code,
            strtoupper($detail->transaction->type),
            $detail->transaction->transaction_date->format('d-m-Y'),
            $detail->transaction->market ?? '-',
            $detail->transaction->user->name,
            $detail->item->name ?? 'Item Terhapus',
            
            $hargaTransaksi, // Harga Deal
            $hargaAudit,     // Harga Pembanding (Modal/Jual)
            
            $detail->quantity,
            $detail->subtotal,
            $detail->transaction->grand_total,
            $detail->transaction->description,
        ];
    }
}