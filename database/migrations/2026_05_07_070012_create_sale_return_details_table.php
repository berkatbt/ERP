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
        Schema::create('sale_return_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_return_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained(); // Produk yang direturn

            $table->integer('qty'); // Jumlah produk yang direturn

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_return_details');
    }
};
