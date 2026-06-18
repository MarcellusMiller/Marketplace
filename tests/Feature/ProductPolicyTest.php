<?php

use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(RoleSeeder::class);
});

it("allows guests to view products", function () {
    /** @var Product $product */
    $product = Product::factory()->create();

    expect(Gate::allows("viewAny", Product::class))->toBeTrue();
    expect(Gate::allows("view", $product))->toBeTrue();
});

it("prevents buyers from creating products", function () {
    /** @var User $buyer */
    $buyer = User::factory()->create();
    $buyer->assignRole("buyer");

    expect($buyer->can("create", Product::class))->toBeFalse();
});

it("allows sellers to create products", function () {
    /** @var User $seller */
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    expect($seller->can("create", Product::class))->toBeTrue();
});

it("allows admins to create products", function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole("admin");

    expect($admin->can("create", Product::class))->toBeTrue();
});

it("allows sellers to update their own products", function () {
    /** @var User $seller */
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    /** @var Product $product */
    $product = Product::factory()
        ->for($seller, "seller")
        ->create();

    expect($seller->can("update", $product))->toBeTrue();
});

it("prevents sellers from updating another seller products", function () {
    /** @var User $seller */
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    /** @var User $anotherSeller */
    $anotherSeller = User::factory()->create();
    $anotherSeller->assignRole("seller");

    /** @var Product $product */
    $product = Product::factory()
        ->for($anotherSeller, "seller")
        ->create();

    expect($seller->can("update", $product))->toBeFalse();
});

it("allows admins to update any product", function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole("admin");

    /** @var Product $product */
    $product = Product::factory()->create();

    expect($admin->can("update", $product))->toBeTrue();
});

it("allows sellers to delete their own products", function () {
    /** @var User $seller */
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    /** @var Product $product */
    $product = Product::factory()
        ->for($seller, "seller")
        ->create();

    expect($seller->can("delete", $product))->toBeTrue();
});

it("prevents sellers from deleting another seller products", function () {
    /** @var User $seller */
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    /** @var User $anotherSeller */
    $anotherSeller = User::factory()->create();
    $anotherSeller->assignRole("seller");

    /** @var Product $product */
    $product = Product::factory()
        ->for($anotherSeller, "seller")
        ->create();

    expect($seller->can("delete", $product))->toBeFalse();
});

it("allows admins to delete any product", function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole("admin");

    /** @var Product $product */
    $product = Product::factory()->create();

    expect($admin->can("delete", $product))->toBeTrue();
});
