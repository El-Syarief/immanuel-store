<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- Wajib ada

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes; // <--- Pasang Trait ini

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'role', // 'admin' atau 'cashier'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'username_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELASI ---

    // User (Admin/Kasir) bisa punya banyak transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // User (Admin) bisa punya banyak riwayat input item baru
    public function createdItems()
    {
        return $this->hasMany(Item::class, 'created_by');
    }

    // User (Admin) bisa punya banyak riwayat perubahan harga
    public function priceHistories()
    {
        return $this->hasMany(ItemPriceHistory::class);
    }
}