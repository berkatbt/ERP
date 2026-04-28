<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $branchA = Branch::create([
            'name' => 'Cabang Pusat',
            'address' => 'Jalan Utama No. 1',
        ]);

        // Tambahkan seeder produk
        $this->call([
            ProductSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
