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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->enum('type', ['IN', 'OUT']); // IN = masuk, OUT = keluar
            $table->integer('qty'); // jumlah
            $table->string('reference'); // referensi (Pembelian, Penjualan, Retur, Adjustment)
            $table->integer('reference_id')->nullable(); // ID dari referensi (purchase_id, sale_id, etc)
            $table->timestamps();

            $table->index(['product_id', 'branch_id']);
            $table->index(['reference', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
