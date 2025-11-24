<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang</title>
    <style>
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align: center">Laporan Data Barang</h2>
    <table>
        <thead>
            <tr>
                <th>Kode</th><th>Nama</th><th>Market</th><th>Kriteria</th><th>Stok</th><th>Modal</th><th>Jual</th><th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->code }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->market }}</td>
                <td>{{ $item->criteria }}</td>
                <td>{{ $item->stock }}</td>
                <td>{{ number_format($item->buy_price) }}</td>
                <td>{{ number_format($item->sell_price) }}</td>
                <td>{{ $item->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>