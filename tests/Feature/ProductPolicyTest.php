<?php

use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

it("allows guests to view products", function () {
    $product = Product::factory()->create();

    expect(Gate::allows("viewAny", Product::class))->toBeTrue();
    expect(Gate::allows("view", $product))->toBeTrue();
});

it("prevents buyers from creating products", function () {
    $buyer = User::factory()->create();
    $buyer->assignRole("buyer");

    expect($buyer->can("create", Product::class))->toBeFalse();
});

it("allows sellers to create products", function () {
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    expect($seller->can("create", Product::class))->toBeTrue();
});

it("allows admins to create products", function () {
    $admin = User::factory()->create();
    $admin->assignRole("admin");

    expect($admin->can("create", Product::class))->toBeTrue();
});

it("allows sellers to update their own products", function () {
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    $product = Product::factory()
        ->for($seller, "seller")
        ->create();

    expect($seller->can("update", $product))->toBeTrue();
});

it("prevents sellers from updating another seller products", function () {
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    $anotherSeller = User::factory()->create();
    $anotherSeller->assignRole("seller");

    $product = Product::factory()
        ->for($anotherSeller, "seller")
        ->create();

    expect($seller->can("update", $product))->toBeFalse();
});

it("allows admins to update any product", function () {
    $admin = User::factory()->create();
    $admin->assignRole("admin");

    $product = Product::factory()->create();

    expect($admin->can("update", $product))->toBeTrue();
});

it("allows sellers to delete their own products", function () {
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    $product = Product::factory()
        ->for($seller, "seller")
        ->create();

    expect($seller->can("delete", $product))->toBeTrue();
});

it("prevents sellers from deleting another seller products", function () {
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    $anotherSeller = User::factory()->create();
    $anotherSeller->assignRole("seller");

    $product = Product::factory()
        ->for($anotherSeller, "seller")
        ->create();

    expect($seller->can("delete", $product))->toBeFalse();
});

it("allows admins to delete any product", function () {
    $admin = User::factory()->create();
    $admin->assignRole("admin");

    $product = Product::factory()->create();

    expect($admin->can("delete", $product))->toBeTrue();
});
