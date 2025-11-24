<!DOCTYPE html>
<html>
<head>
    <title>Laporan Detail Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .period { text-align: center; font-size: 9pt; color: #666; margin-bottom: 20px; }
        
        /* Styling untuk setiap Blok Transaksi */
        .transaction-block { margin-bottom: 25px; page-break-inside: avoid; border: 1px solid #ddd; padding: 10px; }
        
        .trx-header { display: table; width: 100%; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .trx-info { display: table-cell; vertical-align: top; }
        .trx-total { display: table-cell; text-align: right; vertical-align: top; font-weight: bold; color: #444; }
        
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8pt; color: white; }
        .bg-in { background-color: #16a34a; } /* Hijau */
        .bg-out { background-color: #4f46e5; } /* Biru */

        /* Tabel Barang */
        .items-table { width: 100%; border-collapse: collapse; font-size: 9pt; margin-top: 5px; }
        .items-table th { background-color: #f9fafb; border-bottom: 1px solid #ddd; padding: 4px; text-align: left; }
        .items-table td { border-bottom: 1px solid #eee; padding: 4px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">LAPORAN DETAIL TRANSAKSI</h2>
        <h3 style="margin:5px 0;">IMMANUEL STORE</h3>
    </div>

    <div class="period">
        Periode: {{ request('date_start') ? date('d M Y', strtotime(request('date_start'))) : '-' }} 
        s/d 
        {{ request('date_end') ? date('d M Y', strtotime(request('date_end'))) : '-' }}
    </div>

    @foreach($transactions as $trx)
        <div class="transaction-block">
            <div class="trx-header">
                <div class="trx-info" style="width: 70%;">
                    <strong>{{ $trx->invoice_code }}</strong> 
                    <span class="badge {{ $trx->type == 'in' ? 'bg-in' : 'bg-out' }}">
                        {{ strtoupper($trx->type) }}
                    </span>
                    <br>
                    <span style="color: #666; font-size: 9pt;">
                        Tgl: {{ $trx->transaction_date->format('d/m/Y') }} | 
                        Kasir: {{ $trx->user->name }} | 
                        Market: {{ $trx->market ?? '-' }}
                    </span>
                </div>
                <div class="trx-total">
                    Grand Total: Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                </div>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">Nama Barang</th>
                        <th style="width: 25%;" class="text-right">Harga (Deal vs Audit)</th>
                        <th style="width: 10%;" class="text-center">Qty</th>
                        <th style="width: 30%;" class="text-right">Subtotal</th>
                        <th style="width: 30%;" class="text-right">Deskripsi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($trx->details as $detail)
                    <tr>
                        <td>{{ $detail->item->name ?? '(Item Dihapus)' }}</td>
                        
                        <td class="text-right">
                            @php
                                // Harga Transaksi
                                $hargaTransaksi = $detail->price;
                                if ($hargaTransaksi <= 0) {
                                    $hargaTransaksi = ($trx->type == 'in') ? $detail->buy_price_snapshot : $detail->sell_price_snapshot;
                                }

                                // Harga Audit
                                $hargaAudit = ($trx->type == 'in') ? $detail->sell_price_snapshot : $detail->buy_price_snapshot;
                                
                                // Label Audit
                                $labelAudit = ($trx->type == 'in') ? 'Jual:' : 'Modal:';
                            @endphp
                            
                            <div style="font-weight: bold;">
                                Rp {{ number_format($hargaTransaksi, 0, ',', '.') }}
                            </div>
                            
                            <div style="font-size: 8pt; color: #666;">
                                {{ $labelAudit }} {{ number_format($hargaAudit, 0, ',', '.') }}
                            </div>
                        </td>

                        <td class="text-center">{{ $detail->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $trx->description }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

</body>
</html>