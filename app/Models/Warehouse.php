<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $guarded = ['id'];

    // Relasi: Satu Gudang punya banyak Barang
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_warehouse');
    }
}