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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode Barang
            $table->string('name');           // Nama Barang
            $table->string('criteria')->nullable();           // Kriteria Barang
            $table->integer('stock')->default(0);

            // Harga Beli (Modal) & Harga Jual (Umum)
            $table->decimal('buy_price', 15, 2)->default(0); // Harga kulakan/modal
            $table->decimal('sell_price', 15, 2)->default(0); // Harga jual ke customer
            
            $table->text('description')->nullable();
            
            // kolom siapa pembuatnya
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes(); // <--- PENTING BIAR RIWAYAT AMAN
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
