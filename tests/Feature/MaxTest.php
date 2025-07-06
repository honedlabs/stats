<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use Honed\Stats\Max;
use Honed\Stats\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create();

    Product::factory(10)
        ->for($this->user)
        ->create();

    $this->max = DB::table('products')
        ->where('user_id', $this->user->id)
        ->max('price');
});

it('creates max stat', function () {
    $stat = Max::make('products_max_price', 'Max');

    expect($stat)
        ->toBeInstanceOf(Max::class)
        ->getName()->toBe('products_max_price')
        ->getLabel()->toBe('Max')
        ->getValue()->toBeInstanceOf(Closure::class);
});

it('infers aggregate relationship', function () {
    $stat = Max::make('products_max_price', 'Max');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->max);
});

it('specifies aggregate column', function () {
    $stat = Stat::make('price', 'Max')
        ->max('products', 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->max);
});

it('specifies array relationship', function () {
    $stat = Stat::make('price', 'Max')
        ->max([
            'products' => fn (Builder $query) => $query,
        ], 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->max);
});

it('specifies array relationship with aliasing', function () {
    $stat = Stat::make('price', 'Max')
        ->max([
            'products as expensive_products' => fn (Builder $query) => $query,
        ], 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->max);
});

it('throws exception when no column is not specified', function () {
    Stat::make('products', 'Max')
        ->max('products');
})->throws(InvalidArgumentException::class);
