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
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();

            $table->foreign('sale_id')->constrained()->casecadeOnDelete();
            $table->foreignId('user_id')->constrained(); // User yang membuat return
            $table->foreignId('branch_id')->constrained(); // Cabang tempat return dilakukan

            $table->string('reason')->nullable(); // Alasan return

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_returns');
    }
};
