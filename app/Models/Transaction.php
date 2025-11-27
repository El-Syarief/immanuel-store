<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    // Kita proteksi 'id', sisanya boleh diisi massal
    protected $guarded = ['id']; 

    // Casting agar tanggal otomatis jadi objek Carbon (mudah diformat)
    protected $casts = [
        'transaction_date' => 'date',
    ];

    // --- RELASI ---

    // Transaksi milik satu User (Kasir/Admin)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Satu transaksi punya BANYAK detail barang
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}