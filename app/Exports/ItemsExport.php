<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        // Load relasi warehouses
        $query = Item::query()->with(['creator', 'warehouses']);

        // Filter Pencarian
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }
        // Filter Kriteria
        if ($this->request->filled('criteria')) {
            $query->where('criteria', $this->request->criteria);
        }
        // Filter Pembuat
        if ($this->request->filled('creator_id')) {
            $query->where('created_by', $this->request->creator_id);
        }
        // Filter Gudang
        if ($this->request->filled('warehouse_id')) {
            $query->whereHas('warehouses', function($q) {
                $q->where('warehouses.id', $this->request->warehouse_id);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Kode', 
            'Nama Barang', 
            'Akun',
            'Kriteria', 
            'Stok', 
            'Harga Modal ($)', 
            'Harga Jual ($)', 
            'Pembuat', 
            'Tgl Dibuat'
        ];
    }

    public function map($item): array
    {
        // Ambil nama gudang dan gabungkan dengan koma
        $akun = $item->warehouses->pluck('name')->join(', ') ?: '-';

        return [
            $item->code,
            $item->name,
            $akun, // Tampilkan list gudang
            $item->criteria,
            $item->stock,
            (float) $item->buy_price, 
            (float) $item->sell_price,
            $item->creator->name ?? '-',
            $item->created_at->format('d-m-Y'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Header Bold
        ];
    }

    public function columnFormats(): array
    {
        return [
            // Kolom F (Harga Modal) -> Format Accounting USD
            'F' => NumberFormat::FORMAT_ACCOUNTING_USD, 
            
            // Kolom G (Harga Jual) -> Format Accounting USD
            'G' => NumberFormat::FORMAT_ACCOUNTING_USD, 
        ];
    }
}