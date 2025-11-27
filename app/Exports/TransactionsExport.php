<?php

namespace App\Exports;

use App\Models\TransactionDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting; // <--- TAMBAHAN
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;     // <--- TAMBAHAN
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = TransactionDetail::query()->with(['transaction.user', 'item']);

        $query->whereHas('transaction', function (Builder $q) {
            if ($this->request->filled('type')) $q->where('type', $this->request->type);
            if ($this->request->filled('market')) $q->where('market', $this->request->market);
            if ($this->request->filled('user_id')) $q->where('user_id', $this->request->user_id);
            if ($this->request->filled('date_start') && $this->request->filled('date_end')) {
                $q->whereBetween('transaction_date', [$this->request->date_start, $this->request->date_end]);
            }
        });

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
            'Harga Transaksi', // Kolom G
            'Harga Audit',     // Kolom H
            'Qty', 
            'Subtotal Barang', // Kolom J
            'Total Invoice',    // Kolom K
            'Catatan'
        ];
    }

    public function map($detail): array
    {
        $hargaTransaksi = $detail->price;
        if ($hargaTransaksi <= 0) {
            $hargaTransaksi = ($detail->transaction->type == 'in') 
                ? $detail->buy_price_snapshot 
                : $detail->sell_price_snapshot;
        }

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
            
            (float) $hargaTransaksi, // Kirim Angka Mentah (Float)
            (float) $hargaAudit,     // Kirim Angka Mentah
            $detail->quantity,
            (float) $detail->subtotal,      // Kirim Angka Mentah
            (float) $detail->transaction->grand_total, // Kirim Angka Mentah
            $detail->transaction->description,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_ACCOUNTING_USD, // Harga Transaksi
            'H' => NumberFormat::FORMAT_ACCOUNTING_USD, // Harga Audit
            'J' => NumberFormat::FORMAT_ACCOUNTING_USD, // Subtotal
            'K' => NumberFormat::FORMAT_ACCOUNTING_USD, // Total Invoice
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [ 1 => ['font' => ['bold' => true]] ];
    }
}