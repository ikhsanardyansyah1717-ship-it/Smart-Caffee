<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Owner Quattro',
                'username' => 'owner',
                'email' => 'owner@quattrocoffee.com',
                'password' => Hash::make('owner123'),
                'role' => 'owner',
            ],
            [
                'name' => 'Kitchen Quattro',
                'username' => 'kitchen',
                'email' => 'kitchen@quattrocoffee.com',
                'password' => Hash::make('kitchen123'),
                'role' => 'kitchen',
            ],
            [
                'name' => 'Kasir Quattro',
                'username' => 'kasir',
                'email' => 'kasir@quattrocoffee.com',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['username' => $admin['username']],
                $admin
            );
        }
    }
}
