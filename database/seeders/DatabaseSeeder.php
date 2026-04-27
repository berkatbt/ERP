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

        $branchB = Branch::create([
            'name' => 'Cabang Jakarta',
            'address' => 'Jalan Sudirman No. 10',
        ]);

        User::factory()->create([
            'name' => 'Owner User',
            'email' => 'owner@example.com',
            'role' => 'owner',
            'branch_id' => $branchA->id,
        ]);

        User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'role' => 'manager',
            'branch_id' => $branchB->id,
        ]);

        // Tambahkan seeder produk
        $this->call(ProductSeeder::class);

        User::factory()->create([
            'name' => 'Finance Admin',
            'email' => 'finance@example.com',
            'role' => 'finance',
            'branch_id' => $branchA->id,
        ]);

        User::factory()->create([
            'name' => 'Warehouse Admin',
            'email' => 'warehouse@example.com',
            'role' => 'warehouse',
            'branch_id' => $branchB->id,
        ]);

        User::factory()->create([
            'name' => 'Cashier User',
            'email' => 'cashier@example.com',
            'role' => 'cashier',
            'branch_id' => $branchB->id,
        ]);
    }
}
