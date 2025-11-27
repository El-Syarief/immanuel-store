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
        // 1. Tabel Master Gudang (Hanya ID & Nama)
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Gudang Pusat", "Toko Cabang"
            $table->timestamps();
        });

        // 2. Tabel Pivot (Penghubung Item <-> Gudang)
        Schema::create('item_warehouse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            
            // Mencegah duplikasi (Satu barang tidak boleh punya 2 label gudang yang sama)
            $table->unique(['item_id', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses_tables');
    }
};
