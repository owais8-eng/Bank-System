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
        User::updateOrCreate(
            ['email'=> 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
            );
            User::updateOrCreate(
            ['email'=> 'manager@gmail.com'],
            [
                'name' => 'manager',
                'email' => 'manager@gmail.com',
                'password' => Hash::make('manager123'),
                'role' => 'manager',
            ]
            );
            User::updateOrCreate(
            ['email'=> 'teller@gmail.com'],
            [
                'name' => 'teller',
                'email' => 'teller@gmail.com',
                'password' => Hash::make('teller'),
                'role' => 'teller',
            ]
            );
    }
}
