<?php

namespace App\Actions\Cart;

use App\Models\Cart;
use App\Models\User;

class GetOrCreateCartAction
{
    public function execute(User $user): Cart
    {
        $cart = Cart::firstOrCreate([
            "user_id" => $user->id,
        ]);

        return $cart->load([
            "items.product.category",
            "items.product.images",
        ]);
    }
}
