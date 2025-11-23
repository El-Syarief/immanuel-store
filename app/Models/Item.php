<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- Wajib ada

class Item extends Model
{
    use SoftDeletes; // <--- Pasang Trait ini

    protected $fillable = [
        'code', 
        'name',
        'market',
        'criteria', 
        'stock', 
        'buy_price', 
        'sell_price', 
        'description', 
        'created_by'
    ];

    // --- RELASI ---

    // Item dibuat oleh satu user (Admin)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Item bisa ada di banyak detail transaksi
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // Item punya riwayat perubahan harga
    public function priceHistories()
    {
        return $this->hasMany(ItemPriceHistory::class);
    }
}