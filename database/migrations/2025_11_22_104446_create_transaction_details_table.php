<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_transaction_details_table.php

    public function up(): void
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            
            // Connect ke tabel transactions
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            
            // Connect ke 'id' di tabel item
            $table->foreignId('item_id')->constrained(); 
            
            $table->integer('quantity');

            // Simpan dua harga untuk keperluan laporan
            // Jika tipe transaksi = 'out' (Jual), maka:
            // buy_price_snapshot = harga modal item saat itu
            // sell_price_snapshot = harga jual ke customer
            $table->decimal('buy_price_snapshot', 15, 2)->default(0); 
            $table->decimal('sell_price_snapshot', 15, 2)->default(0);

            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
