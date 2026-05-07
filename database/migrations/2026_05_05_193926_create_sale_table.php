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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Foreign key to users table
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete(); // Foreign key to brands table

            $table->string('invoice')->unique();

            $table->decimal('total', 15, 2)->default(0);

            $table->enum('payment_type', ['cash', 'credit']); // Payment type: cash or credit
            $table->enum('status', ['paid', 'unpaid'])->default('paid');

            $table->foreignId('customer_id')->nullable()->constrained(); // Foreign key to customers table, nullable for walk-in customers

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale');
    }
};
