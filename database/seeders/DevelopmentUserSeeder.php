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
        $buyer = User::factory()->create([
            "name" => "Marcellus buyer",
            "email" => "buyer@example.com",
            "password" => "password123",
            
        ]);

        $buyer->assignRole("buyer");

        $seller = User::factory()->create([
            "name" => "Marcellus seller",
            "email" => "seller@example.com",
            "password" => "password123",
            
        ]);

        $seller->assignRole("seller");

        $admin = User::factory()->create([
            "name" => "Marcellus admin",
            "email" => "admin@example.com",
            "password" => "password123",
        ]);

        $admin->assignRole("admin");
    }
}
