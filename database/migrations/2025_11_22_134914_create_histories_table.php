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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            
            // Harga (Lama & Baru)
            $table->decimal('old_buy_price', 15, 2)->nullable(); // <--- BARU
            $table->decimal('new_buy_price', 15, 2)->nullable(); // Ganti nama 'buy_price' biar konsisten
            
            $table->decimal('old_sell_price', 15, 2)->nullable(); // <--- BARU
            $table->decimal('new_sell_price', 15, 2)->nullable(); // Ganti nama 'sell_price'
            
            // Stok
            $table->integer('old_stock')->nullable();
            $table->integer('new_stock')->nullable();

            // Market
            $table->string('old_market')->nullable();
            $table->string('new_market')->nullable();

            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
