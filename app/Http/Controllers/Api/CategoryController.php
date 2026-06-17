<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Actions\Categories\ListCategoriesAction;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index(ListCategoriesAction $action)
    {
        $categories = $action->execute();

        return CategoryResource::collection($categories);
    }
}
