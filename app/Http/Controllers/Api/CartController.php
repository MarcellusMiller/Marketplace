<?php

namespace App\Http\Controllers\Api;

use App\Actions\Cart\GetOrCreateCartAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request, GetOrCreateCartAction $action)
    {
        $cart = $action->execute($request->user());

        return new CartResource($cart)
            ->response()
            ->setStatusCOde(200);
    }
}
