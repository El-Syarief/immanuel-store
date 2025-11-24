<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    // OPSI 1 (Paling Praktis): Izinkan semua kolom KECUALI id
    protected $guarded = ['id']; 

    /* // OPSI 2 (Manual): Jika kamu tetap ingin pakai $fillable, 
    // pastikan 'price' ada di sini:
    protected $fillable = [
        'transaction_id', 
        'item_id', 
        'quantity', 
        'price',
        'subtotal', 
        'buy_price_snapshot', 
        'sell_price_snapshot'
    ]; 
    */

    // --- RELASI ---
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }
}