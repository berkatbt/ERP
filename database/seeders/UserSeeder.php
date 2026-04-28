<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Owner',
                'email' => 'owner@example.com',
                'role_id' => 1,
                'branch_id' => 1,
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@example.com',
                'role_id' => 2,
                'branch_id' => 1,
            ],
            [
                'name' => 'Finance Admin',
                'email' => 'finance@example.com',
                'role_id' => 3,
                'branch_id' => 1,
            ],
            [
                'name' => 'Warehouse Admin',
                'email' => 'warehouse@example.com',
                'role_id' => 4,
                'branch_id' => 1,
            ],
            [
                'name' => 'Cashier',
                'email' => 'cashier@example.com',
                'role_id' => 5,
                'branch_id' => 1,
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'role_id' => $user['role_id'],
                'branch_id' => $user['branch_id'],
                'password' => Hash::make('password')
            ]);
        }
    }
}
