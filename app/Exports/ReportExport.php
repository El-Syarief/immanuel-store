<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromArray, WithHeadings, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $d = $this->data;
        
        // Kita susun baris demi baris secara manual agar rapi di Excel
        return [
            ['Periode', $d['startDate'] . ' s/d ' . $d['endDate']],
            [''], // Spasi kosong
            ['I. LAPORAN LABA RUGI'],
            ['Total Omzet (Penjualan Kotor)', number_format($d['omzet'])],
            ['Total Modal Barang Terjual (HPP)', number_format($d['hpp'])],
            ['LABA BERSIH (Profit)', number_format($d['profit'])],
            [''],
            ['II. ARUS KAS STOK'],
            ['Total Belanja Stok Baru (Uang Keluar)', number_format($d['totalPurchase'])],
            [''],
            ['III. NERACA ASET (Per Akhir Periode)'],
            ['Valuasi Aset Gudang', number_format($d['assetValue'])],
            ['Total Item Terjual', number_format($d['totalSold'])],
            ['Total Item Dibeli', number_format($d['totalPurchased'])],
            [''],
            ['IV. TOP 5 BARANG TERLARIS'],
            ['Nama Barang', 'Qty Terjual', 'Total Omzet'],
            ...$d['topItems']->map(fn($i) => [$i['name'], $i['qty'], number_format($i['total'])])
        ];
    }

    public function headings(): array
    {
        return ['LAPORAN KEUANGAN DETAILED', ''];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // Judul Besar
            3 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]], // Header Section
            8 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
            11 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
            16 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
        ];
    }
}