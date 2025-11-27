<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; vertical-align: top; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-right { text-align: right; }
        .badge { 
            display: inline-block; 
            padding: 2px 5px; 
            font-size: 8pt; 
            background-color: #e5e7eb; 
            border-radius: 4px; 
            margin-right: 3px; 
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <h2>LAPORAN DATA BARANG</h2>
    
    <div style="margin-bottom: 15px; font-size: 9pt;">
        <strong>Total Barang:</strong> {{ count($items) }} Item<br>
        <strong>Dicetak pada:</strong> {{ date('d F Y, H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%">Kode</th>
                <th style="width: 25%">Nama Barang</th>
                <th style="width: 25%">Akun</th>
                <th style="width: 10%">Stok</th>
                <th style="width: 15%">Modal ($)</th>
                <th style="width: 15%">Jual ($)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->code }}</td>
                <td>
                    <b>{{ $item->name }}</b><br>
                    <small style="color: #666;">{{ $item->criteria ?? '' }}</small>
                </td>
                <td>
                    @if($item->warehouses->isNotEmpty())
                        @foreach($item->warehouses as $wh)
                            <span class="badge">{{ $wh->name }}</span>
                        @endforeach
                    @else
                        <i style="color: #999;">-</i>
                    @endif
                </td>
                <td style="text-align: center;">{{ $item->stock }}</td>
                <td class="text-right">${{ number_format($item->buy_price, 2, '.', ',') }}</td>
                <td class="text-right">${{ number_format($item->sell_price, 2, '.', ',') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>