<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $d = $this->data;
        $topItems = $d['topItems'] ?? collect([]);

        // FORMAT BARU:
        // Col A: Label
        // Col B: Nilai Uang ($)
        // Col C: Nilai Jumlah (Qty)

        $rows = [
            ['Periode', ($d['startDate'] ?? '-') . ' s/d ' . ($d['endDate'] ?? '-')],
            [''], 
            
            // SECTION I: KEUANGAN (Uang masuk ke Kolom B)
            ['I. RINGKASAN KEUANGAN', '', ''],
            ['Total Omzet (Penjualan Kotor)', (float) ($d['omzet'] ?? 0), null],
            ['Total Modal Barang Terjual (HPP)', (float) ($d['hpp'] ?? 0), null],
            ['LABA BERSIH', (float) ($d['profit'] ?? 0), null],
            ['Total Belanja Stok Baru', (float) ($d['totalPurchase'] ?? 0), null],
            ['Valuasi Aset Gudang', (float) ($d['assetValue'] ?? 0), null],
            [''],

            // SECTION II: STATISTIK (Qty masuk ke Kolom C)
            ['II. STATISTIK VOLUME', '', ''],
            ['Total Item Terjual', null, (float) ($d['totalSold'] ?? 0)],
            ['Total Item Dibeli', null, (float) ($d['totalPurchased'] ?? 0)],
            ['Total Stok Fisik Tersisa', null, (float) ($d['totalStockRemaining'] ?? 0)],
            ['Jumlah Transaksi Berhasil', null, (float) ($d['trxCount'] ?? 0)],
            [''],

            // SECTION III: TOP ITEMS
            // Format: Nama | Omzet ($) | Qty (Num)
            ['III. 5 BARANG TERLARIS', '', ''],
            ['Nama Barang', 'Kontribusi Omzet ($)', 'Qty Terjual (Pcs)'],
        ];

        foreach ($topItems as $item) {
            $rows[] = [
                $item['name'],
                (float) $item['total'], // Masuk Kolom B (Uang)
                (float) $item['qty']    // Masuk Kolom C (Angka)
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['LAPORAN KEUANGAN DETAILED', '', ''];
    }

    public function columnFormats(): array
    {
        return [
            // Kolom B Khusus Uang (Dollar)
            'B' => NumberFormat::FORMAT_ACCOUNTING_USD,
            
            // Kolom C Khusus Angka (Tanpa Desimal, Pakai Koma Ribuan)
            'C' => '#,##0', 
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style Header per Section
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']] // Indigo
        ];

        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            
            // Header Section I
            3 => $headerStyle,
            // Header Section II
            10 => $headerStyle,
            // Header Section III
            15 => $headerStyle,
            // Header Tabel Barang
            16 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E5E7EB']]],
        ];
    }
}