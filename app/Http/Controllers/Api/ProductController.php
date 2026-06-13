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
            category : $request->query("category"),
            search : $request->query("search")
        );

        return ProductResource::collection($products);
    }
}
