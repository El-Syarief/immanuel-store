<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $guarded = ['id'];

    // --- RELASI ---

    // Detail ini milik satu Transaksi (Header)
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Detail ini merujuk ke satu Item
    // Walaupun item di-soft delete, relasi ini tetap jalan (withTrashed otomatis diurus Laravel kalau mau, atau manual)
    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed(); // Tambahkan withTrashed() agar bisa baca item yg sudah dihapus
    }
}