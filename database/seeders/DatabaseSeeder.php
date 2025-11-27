<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 1 Akun Admin
        User::create([
            'name' => 'Tester Admin',
            'username' => 'admin tester', // Username untuk login
            'password' => Hash::make('admintester123'), // Password default
            'role' => 'admin',
            'username_verified_at' => now(),
        ]);

        // Buat 1 Akun Kasir (Contoh)
        User::create([
            'name' => 'Kasir Tester',
            'username' => 'kasir tester',
            'password' => Hash::make('kasirtester123'),
            'role' => 'cashier',
            'username_verified_at' => now(),
        ]);

        \App\Models\Warehouse::insert([
            ['name' => 'tomfel018', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'yasaasliwoi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'tomfel083', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}