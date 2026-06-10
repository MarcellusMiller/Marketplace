<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::role("seller")->first();

        if(! $seller) {
            return;
        }

        $categories = Category::all();

        foreach($categories as $category) {
            Product::factory()
                ->count(3)
                ->for($seller, "seller")
                ->for($category)
                ->create()
                ->each(function (Product $product) {
                    ProductImage::factory()
                        ->for($product)
                        ->main()
                        ->create();

                    ProductImage::factory()
                        ->count(2)
                        ->for($product)
                        ->create();
                });
        }
    }
}
