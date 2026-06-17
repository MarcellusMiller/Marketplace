<?php

namespace App\Actions\Categories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class ListCategoriesAction
{
    public function execute(): Collection
    {
        return Category::query()
            ->orderBy("name")
            ->get();
    }
}
