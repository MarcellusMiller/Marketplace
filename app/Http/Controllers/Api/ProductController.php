<?php

namespace App\Http\Controllers\Api;

use App\Actions\Products\ListProductsAction;
use App\Http\Resources\ProductResource;
use App\Actions\Products\ListSingleProductAction;
use App\Http\Resources\ProductDetailResource;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

use App\Actions\Products\CreateProductAction;
use App\Actions\Products\UpdateProductAction;

use App\Http\Requests\Api\Products\UpdateProductRequest;
use App\Http\Requests\Api\Products\StoreProductRequest;

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

    public function show(Product $product, ListSingleProductAction $action) 
    {
        $product = $action->execute($product);

        return new ProductDetailResource($product);
    }
    public function store(StoreProductRequest $request, CreateProductAction $action) 
    {
        $product = $action->execute(
            seller: $request->user(),
            data: $request->validated(),
        );

        return (new ProductDetailResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function update(
        UpdateProductRequest $request,
        Product $product,
        UpdateProductAction $action,
    ) {
        $product = $action->execute(
            product: $product,
            data: $request->validated(),
        );

        return new ProductDetailResource($product);
    }
}
