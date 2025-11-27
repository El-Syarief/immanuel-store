<!DOCTYPE html>
<html>
<head>
    <title>Laporan Detail Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .period { text-align: center; font-size: 9pt; color: #666; margin-bottom: 20px; }
        
        /* Blok per Invoice */
        .transaction-block { margin-bottom: 25px; page-break-inside: avoid; border: 1px solid #ddd; padding: 10px; }
        
        .trx-header { width: 100%; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 5px; }
        .trx-title { font-weight: bold; font-size: 11pt; }
        .trx-meta { font-size: 9pt; color: #555; }
        
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8pt; color: white; font-weight: bold; }
        .bg-in { background-color: #16a34a; } /* Hijau */
        .bg-out { background-color: #4f46e5; } /* Biru */

        /* Tabel Barang */
        .items-table { width: 100%; border-collapse: collapse; font-size: 9pt; margin-top: 5px; }
        .items-table th { background-color: #f3f4f6; border-bottom: 1px solid #ccc; padding: 5px; text-align: left; }
        .items-table td { border-bottom: 1px solid #eee; padding: 5px; vertical-align: top; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .audit-price { font-size: 8pt; color: #666; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">LAPORAN DETAIL TRANSAKSI</h2>
        <h3 style="margin:5px 0;">IMMANUEL STORE</h3>
    </div>

    <div class="period">
        Periode: {{ request('date_start') ? date('d M Y', strtotime(request('date_start'))) : 'Awal' }} 
        s/d 
        {{ request('date_end') ? date('d M Y', strtotime(request('date_end'))) : 'Sekarang' }}
    </div>

    @foreach($transactions as $trx)
        <div class="transaction-block">
            <table class="trx-header">
                <tr>
                    <td>
                        <span class="trx-title">{{ $trx->invoice_code }}</span>
                        <span class="badge {{ $trx->type == 'in' ? 'bg-in' : 'bg-out' }}">
                            {{ strtoupper($trx->type) }}
                        </span>
                        <br>
                        <span class="trx-meta">
                            {{ $trx->transaction_date->format('d/m/Y') }} | 
                            Kasir: {{ $trx->user->name }} | 
                            Gudang: {{ $trx->warehouse->name ?? 'Semua' }} |
                            {{ $trx->market ? 'Market: '.$trx->market : '' }}
                        </span>
                    </td>
                    <td class="text-right" style="vertical-align: top;">
                        <strong>Total: $ {{ number_format($trx->grand_total, 2, '.', ',') }}</strong>
                    </td>
                </tr>
            </table>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Nama Barang</th>
                        <th style="width: 25%;" class="text-right">Harga (Deal vs Audit)</th>
                        <th style="width: 10%;" class="text-center">Qty</th>
                        <th style="width: 25%;" class="text-right">Subtotal</th>
                        <th style="width: 25%;" class="text-center">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trx->details as $detail)
                    <tr>
                        <td>{{ $detail->item->name ?? '(Item Dihapus)' }}</td>
                        
                        <td class="text-right">
                            @php
                                // Logika Harga Utama
                                $hargaDeal = $detail->price;
                                if ($hargaDeal <= 0) {
                                    $hargaDeal = ($trx->type == 'in') ? $detail->buy_price_snapshot : $detail->sell_price_snapshot;
                                }

                                // Logika Harga Audit
                                $hargaAudit = ($trx->type == 'in') ? $detail->sell_price_snapshot : $detail->buy_price_snapshot;
                                $labelAudit = ($trx->type == 'in') ? 'Ref. Jual:' : 'Ref. Modal:';
                            @endphp
                            
                            <div style="font-weight: bold;">$ {{ number_format($hargaDeal, 2, '.', ',') }}</div>
                            
                            <div class="audit-price">
                                {{ $labelAudit }} $ {{ number_format($hargaAudit, 2, '.', ',') }}
                            </div>
                        </td>

                        <td class="text-center">{{ $detail->quantity }}</td>
                        
                        <td class="text-right">$ {{ number_format($detail->subtotal, 2, '.', ',') }}</td>
                        <td class="text-center">{{ $trx->description}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

</body>
</html>