<?php

namespace App\Http\Controllers\Api;

use App\Actions\Products\ListProductsAction;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(ListProductsAction $action)
    {
        $products = $action->execute();

        return ProductResource::collection($products);
    }
}
