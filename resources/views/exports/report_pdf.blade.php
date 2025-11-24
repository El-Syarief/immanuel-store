<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan Detail</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 12pt; font-weight: bold; background-color: #eee; padding: 5px; border-left: 5px solid #4472C4; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        td { padding: 5px; border-bottom: 1px solid #eee; }
        .amount { text-align: right; font-weight: bold; }
        .sub-label { font-size: 9pt; color: #666; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KEUANGAN & KINERJA</h2>
        <p>Periode: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</p>
    </div>

    <div class="section">
        <div class="section-title">I. RINGKASAN LABA RUGI (INCOME STATEMENT)</div>
        <table>
            <tr>
                <td>
                    Total Omzet Penjualan
                    <div class="sub-label">Total uang masuk dari transaksi penjualan (OUT)</div>
                </td>
                <td class="amount" style="color: #000;">Rp {{ number_format($omzet, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>
                    (-) Harga Pokok Penjualan (HPP)
                    <div class="sub-label">Total modal dari barang yang laku terjual</div>
                </td>
                <td class="amount" style="color: #d32f2f;">(Rp {{ number_format($hpp, 0, ',', '.') }})</td>
            </tr>
            <tr style="background-color: #e8f5e9;">
                <td><strong>LABA BERSIH (NET PROFIT)</strong></td>
                <td class="amount" style="font-size: 12pt; color: #2e7d32;">Rp {{ number_format($profit, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">II. POSISI ASET & INVENTARIS</div>
        <table>
            <tr>
                <td>Valuasi Stok Gudang (Per Akhir Periode)</td>
                <td class="amount">Rp {{ number_format($assetValue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pembelian Stok Baru (Uang Keluar)</td>
                <td class="amount" style="color: orange;">Rp {{ number_format($totalPurchase, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">III. 5 BARANG TERLARIS (TOP SELLING)</div>
        <table style="border: 1px solid #ddd;">
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td>Nama Barang</td>
                <td style="text-align: center;">Qty Terjual</td>
                <td style="text-align: right;">Kontribusi Omzet</td>
            </tr>
            @foreach($topItems as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center;">{{ $item['qty'] }}</td>
                <td style="text-align: right;">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>