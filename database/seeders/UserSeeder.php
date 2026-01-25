<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'name' => 'Branch Manager',
                'email' => 'manager@gmail.com',
                'password' => Hash::make('manager123'),
                'role' => 'manager',
            ]
        );

        User::updateOrCreate(
            ['email' => 'teller@gmail.com'],
            [
                'name' => 'Bank Teller',
                'email' => 'teller@gmail.com',
                'password' => Hash::make('teller123'),
                'role' => 'teller',
            ]
        );

        User::factory()->create([
            'name' => 'Senior Manager',
            'email' => 'senior.manager@bank.com',
            'role' => 'manager',
        ]);

        User::factory()->create([
            'name' => 'Junior Teller',
            'email' => 'junior.teller@bank.com',
            'role' => 'teller',
        ]);

        User::factory()->createMany([
            [
                'name' => 'John Doe',
                'email' => 'john.doe@email.com',
                'role' => 'customer',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@email.com',
                'role' => 'customer',
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob.johnson@email.com',
                'role' => 'customer',
            ],
            [
                'name' => 'Alice Brown',
                'email' => 'alice.brown@email.com',
                'role' => 'customer',
            ],
            [
                'name' => 'Charlie Wilson',
                'email' => 'charlie.wilson@email.com',
                'role' => 'customer',
            ],
        ]);

        User::factory(10)->create([
            'role' => 'customer',
        ]);
    }
}
