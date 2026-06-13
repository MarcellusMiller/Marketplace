<?php

namespace App\Http\Controllers\Api;

use App\Actions\Products\ListProductsAction;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, ListProductsAction $action)
    {
        $products = $action->execute(
            category: $request->query("category"),
            search: $request->query("search"),
            minPrice: $request->has("min_price") ? $request->integer("min_price") : null,
            maxPrice: $request->has("max_price") ? $request->integer("max_price") : null,
            sort: $request->query("sort"),
        );

        return ProductResource::collection($products);
    }
}
