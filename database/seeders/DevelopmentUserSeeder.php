<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DevelopmentUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buyer = User::updateOrCreate(
            ['email' => 'buyer@example.com'],
            [
                'name' => 'Marcellus buyer',
                'password' => 'password123',
            ]
        );

        $buyer->assignRole('buyer');

        $seller = User::updateOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => 'Marcellus seller',
                'password' => 'password123',
            ]
        );

        $seller->assignRole('seller');

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Marcellus admin',
                'password' => 'password123',
            ]
        );

        $admin->assignRole('admin');
    }
}
