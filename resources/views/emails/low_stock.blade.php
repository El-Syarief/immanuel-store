<!DOCTYPE html>
<html>
<head>
    <title>Critical Stock Alert</title>
    <style>
        /* Reset CSS sederhana untuk email client */
        body { margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; color: #333; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 30px; border: 1px solid #ddd; border-top: 5px solid #d32f2f; border-radius: 4px; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { color: #d32f2f; margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 1px; }
        .alert-box { background-color: #ffebee; border-left: 4px solid #d32f2f; padding: 15px; margin-bottom: 20px; color: #b71c1c; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2c3e50; color: #ffffff; padding: 12px; text-align: left; font-size: 14px; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .footer { margin-top: 30px; font-size: 12px; color: #888; text-align: center; border-top: 1px solid #eee; padding-top: 15px; }
        .btn { display: inline-block; background-color: #d32f2f; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>PERINGATAN KRITIS: STOK HABIS</h1>
        </div>

        <p>Yth. Administrator,</p>

        <div class="alert-box">
            PERHATIAN: Sistem mendeteksi kekosongan stok pada inventaris aktif.
        </div>

        <p>Transaksi terakhir telah menyebabkan saldo stok barang berikut mencapai <strong>NOL (0)</strong>. Mohon segera lakukan pengadaan barang (Restock) untuk mencegah hilangnya potensi penjualan (Lost Sales).</p>

        <table>
            <thead>
                <tr>
                    <th>KODE SKU</th>
                    <th>NAMA BARANG</th>
                    <th style="text-align: center;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td style="font-family: monospace; font-weight: bold;">{{ $item['code'] }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td style="color: #d32f2f; font-weight: 800; text-align: center; background-color: #fff5f5;">
                            OUT OF STOCK (0)
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <br>
        <p>Email ini dikirim secara otomatis oleh sistem. Harap segera menindaklanjuti laporan ini.</p>

        <div class="footer">
            &copy; {{ date('Y') }} Immanuel Store Internal System. All rights reserved.<br>
            Automated Inventory Management Notification.
        </div>
    </div>

</body>
</html>