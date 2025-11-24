<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $guarded = ['id'];

    // --- RELASI ---

    // History milik satu Item
    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    // History dibuat oleh satu Admin
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}