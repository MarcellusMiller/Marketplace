<?php

namespace App\Http\Controllers\Api;

use App\Actions\Cart\GetOrCreateCartAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Actions\Cart\AddCartItemAction;
use App\Http\Requests\Api\Cart\AddCartItemRequest;
use App\Actions\Cart\UpdateCartItemAction;
use App\Http\Requests\Api\Cart\UpdateCartItemRequest;
use App\Actions\Cart\RemoveCartItemAction;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request, GetOrCreateCartAction $action)
    {
        $cart = $action->execute($request->user());

        return new CartResource($cart)
            ->response()
            ->setStatusCode(200);
    }

    public function addItem(AddCartItemRequest $request, AddCartItemAction $action) {
        $cart = $action->execute(
            user: $request->user(),
            data: $request->validated(),
        );

        return (new CartResource($cart))
            ->response()
            ->setStatusCode(200);
    }

    public function updateItem(
        UpdateCartItemRequest $request,
        CartItem $cartItem,
        UpdateCartItemAction $action,
    ) {
        $cart = $action->execute(
            cartItem: $cartItem,
            data: $request->validated(),
        );

        return (new CartResource($cart))
            ->response()
            ->setStatusCode(200);
    }

    public function removeItem(CartItem $cartItem, RemoveCartItemAction $action)
    {
        if($cartItem->cart?->user_id !== request()->user()?->id) {
            abort(403);
        }

        $cart = $action->execute($cartItem);

        return (new CartResource($cart))
            ->response()
            ->setStatusCode(200);
    }
}
