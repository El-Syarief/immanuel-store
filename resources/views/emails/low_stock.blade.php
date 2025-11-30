<!DOCTYPE html>
<html>
<head>
    <title>Alert Stok</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2 style="color: red;">Peringatan Stok Kritis</h2>
    <p>Halo Admin,</p>
    <p>Sistem mendeteksi transaksi terbaru menyebabkan stok barang berikut menjadi <strong>SANGAT SEDIKIT (≤ 1)</strong>:</p>
    
    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr style="background-color: #f3f3f3;">
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Sisa Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['code'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td style="color: red; font-weight: bold;">{{ $item['stock'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Mohon segera lakukan restock / pembelian barang.</p>
    <p>Terima Kasih,<br>Sistem Inventaris</p>
</body>
</html>