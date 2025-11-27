<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .section { margin-bottom: 25px; }
        .section-title { 
            font-size: 11pt; font-weight: bold; 
            background-color: #e0e7ff; color: #3730a3;
            padding: 5px 10px; border-left: 5px solid #3730a3; 
            margin-bottom: 10px; 
        }
        
        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px; border-bottom: 1px solid #eee; }
        .amount { text-align: right; font-weight: bold; font-family: monospace; font-size: 11pt; }
        .sub-label { font-size: 8pt; color: #666; font-style: italic; display: block; margin-top: 2px; }
        
        /* Warna Angka */
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .text-orange { color: #d97706; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">LAPORAN KEUANGAN & KINERJA</h2>
        <h3 style="margin:5px 0;">IMMANUEL STORE</h3>
        <p style="margin:0; font-size: 9pt; color: #666;">
            Periode: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}
        </p>
    </div>

    <div class="section">
        <div class="section-title">I. RINGKASAN LABA RUGI (INCOME STATEMENT)</div>
        <table>
            <tr>
                <td>
                    Total Omzet Penjualan
                    <span class="sub-label">Total pendapatan kotor dari transaksi penjualan (OUT)</span>
                </td>
                <td class="amount">${{ number_format($omzet, 2, '.', ',') }}</td>
            </tr>
            <tr>
                <td>
                    (-) Harga Pokok Penjualan (HPP)
                    <span class="sub-label">Total modal awal dari barang yang berhasil terjual</span>
                </td>
                <td class="amount text-orange">(${{ number_format($hpp, 2, '.', ',') }})</td>
            </tr>
            <tr style="background-color: #f0fdf4;">
                <td style="padding-top: 10px; padding-bottom: 10px;"><strong>LABA BERSIH (NET PROFIT)</strong></td>
                <td class="amount text-green" style="font-size: 14pt;">
                    ${{ number_format($profit, 2, '.', ',') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">II. POSISI ASET & STOK (BALANCE SHEET)</div>
        <table>
            <tr>
                <td>
                    Valuasi Aset Gudang (Estimasi)
                    <span class="sub-label">Nilai total stok barang yang tersisa di gudang per tanggal {{ date('d/m/Y', strtotime($valuationDate)) }}</span>
                </td>
                <td class="amount">${{ number_format($assetValue, 2, '.', ',') }}</td>
            </tr>
            <tr>
                <td>
                    Total Belanja Stok Baru (Cash Out)
                    <span class="sub-label">Uang yang dikeluarkan untuk kulakan barang (Transaksi IN) periode ini</span>
                </td>
                <td class="amount text-red">(${{ number_format($totalPurchase, 2, '.', ',') }})</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">III. STATISTIK VOLUME</div>
        <table>
            <tr>
                <td>Total Item Terjual (Qty)</td>
                <td class="amount" style="color: #333;">{{ number_format($totalSold) }} Unit</td>
            </tr>
            <tr>
                <td>Total Item Dibeli/Masuk (Qty)</td>
                <td class="amount" style="color: #333;">{{ number_format($totalPurchased) }} Unit</td>
            </tr>
            <tr>
                <td>Jumlah Transaksi Berhasil</td>
                <td class="amount" style="color: #333;">{{ number_format($trxCount) }} Kali</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">IV. 5 BARANG TERLARIS (TOP SELLING)</div>
        <table style="border: 1px solid #ddd; margin-top: 5px;">
            <thead>
                <tr style="background-color: #f9fafb; font-weight: bold;">
                    <td style="border-bottom: 2px solid #ddd;">Nama Barang</td>
                    <td style="text-align: center; border-bottom: 2px solid #ddd;">Qty Terjual</td>
                    <td style="text-align: right; border-bottom: 2px solid #ddd;">Kontribusi Omzet</td>
                </tr>
            </thead>
            <tbody>
                @foreach($topItems as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td style="text-align: center;">{{ $item['qty'] }}</td>
                    <td style="text-align: right;">${{ number_format($item['total'], 2, '.', ',') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>