<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_price_histories', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Barang
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            
            // Relasi ke Admin yang mengubah harga
            $table->foreignId('user_id')->constrained(); 
            
            // catat dua-duanya agar riwayatnya lengkap (Modal & Jual)
            $table->decimal('buy_price', 15, 2); // Harga Modal Baru
            $table->decimal('sell_price', 15, 2); // Harga Jual Baru

            // Stok
            $table->integer('old_stock')->nullable();
            $table->integer('new_stock')->nullable();

            // Market (Supplier) - TAMBAHAN BARU
            $table->string('old_market')->nullable();
            $table->string('new_market')->nullable();
            
            // Opsional: Alasan perubahan
            $table->string('reason')->nullable(); 
            
            // created_at akan otomatis menjadi "Tanggal Perubahan"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_price_histories');
    }
};
