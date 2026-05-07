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
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();

            $table->foreignId('branch_id')->constrained();

            $table->decimal('total_debt', 15, 2);

            $table->decimal('paid_amount', 15, 2)->default(0);

            $table->decimal('remaining_amount', 15, 2);

            $table->enum('status', [
                'unpaid',
                'partial',
                'paid',
            ])->default('unpaid');

            $table->date('due_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivables');
    }
};
