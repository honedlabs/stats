<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use Honed\Stats\Min;
use Honed\Stats\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create();

    Product::factory(10)
        ->for($this->user)
        ->create();

    $this->min = DB::table('products')
        ->where('user_id', $this->user->id)
        ->min('price');
});

it('creates min stat', function () {
    $stat = Min::make('products_min_price', 'Min');

    expect($stat)
        ->toBeInstanceOf(Min::class)
        ->getName()->toBe('products_min_price')
        ->getLabel()->toBe('Min')
        ->getValue()->toBeInstanceOf(Closure::class);
});

it('infers aggregate relationship', function () {
    $stat = Min::make('products_min_price', 'Min');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->min);
});

it('specifies aggregate column', function () {
    $stat = Stat::make('price', 'Min')
        ->min('products', 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->min);
});

it('specifies array relationship', function () {
    $stat = Stat::make('price', 'Min')
        ->min([
            'products' => fn (Builder $query) => $query,
        ], 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->min);
});

it('specifies array relationship with aliasing', function () {
    $stat = Stat::make('price', 'Min')
        ->min([
            'products as expensive_products' => fn (Builder $query) => $query,
        ], 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->min);
});

it('throws exception when no column is not specified', function () {
    Stat::make('products', 'Min')
        ->min('products');
})->throws(InvalidArgumentException::class);
