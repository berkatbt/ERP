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
        Schema::table('branches', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('address');
            $table->string('email')->unique()->nullable()->after('phone');
            $table->string('photo')->nullable()->after('email');
            $table->string('city')->nullable()->after('photo');
            $table->decimal('latitude', 10, 8)->nullable()->after('city');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->integer('radius')->nullable()->after('longitude')->comment('Radius dalam meter');
            $table->enum('office_type', ['kantor pusat', 'cabang', 'gudang', 'toko', 'lainnya'])->nullable()->after('radius');
            $table->enum('status', ['aktif', 'tidak aktif'])->default('aktif')->after('office_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['phone', 'email', 'photo', 'city', 'latitude', 'longitude', 'radius', 'office_type', 'status']);
        });
    }
};
