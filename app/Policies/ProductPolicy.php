<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // ja deixei como true para que qualquer usuário possa ver os produtos
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Product $product): bool
    {
        // ja deixei como true para que qualquer usuário possa ver os produtos
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // apenas o admin e o seller podem criar produtos
        return $user->hasAnyRole(["admin", "seller"]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        // apenas o admin pode atualizar os produtos, ou somente os produtos que ele é o vendedor
        return $user->hasRole("admin") || $user->id === $product->seller_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        // apenas o admin pode excluir os produtos, ou somente os produtos que ele é o vendedor
        return $user->hasRole("admin") || $user->id === $product->seller_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }
}
