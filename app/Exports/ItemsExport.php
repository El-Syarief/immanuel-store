<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        // Kita gunakan logika filter yang sama dengan di Controller
        $query = Item::query()->with('creator');

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }
        if ($this->request->filled('criteria')) {
            $query->where('criteria', $this->request->criteria);
        }
        if ($this->request->filled('creator_id')) {
            $query->where('created_by', $this->request->creator_id);
        }
        if ($this->request->filled('market')) {
            $query->where('market', $this->request->market);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['Kode', 'Nama Barang', 'Market', 'Kriteria', 'Stok', 'Harga Modal', 'Harga Jual', 'Pembuat', 'Deskripsi', 'Tgl Dibuat'];
    }

    public function map($item): array
    {
        return [
            $item->code,
            $item->name,
            $item->market,
            $item->criteria,
            $item->stock,
            $item->buy_price,
            $item->sell_price,
            $item->creator->name ?? '-',
            $item->description,
            $item->created_at->format('d-m-Y'),
        ];
    }
}