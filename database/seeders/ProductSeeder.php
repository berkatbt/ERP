<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Kertas A4',
                'sku' => 'A4-001',
                'price_buy' => 35000,
                'price_sell' => 40000,
                'min_stock' => 10,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pulpen Biru',
                'sku' => 'PEN-BLU',
                'price_buy' => 2500,
                'price_sell' => 3500,
                'min_stock' => 50,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Map Folder',
                'sku' => 'MAP-001',
                'price_buy' => 5000,
                'price_sell' => 7000,
                'min_stock' => 20,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Stabilo',
                'sku' => 'STB-001',
                'price_buy' => 8000,
                'price_sell' => 10000,
                'min_stock' => 15,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Penghapus',
                'sku' => 'ERS-001',
                'price_buy' => 1500,
                'price_sell' => 2500,
                'min_stock' => 30,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
