<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use Honed\Stats\Stat;
use Honed\Stats\Sum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create();

    Product::factory(10)
        ->for($this->user)
        ->create();

    $this->sum = DB::table('products')
        ->where('user_id', $this->user->id)
        ->sum('price');
});

it('creates sum stat', function () {
    $stat = Sum::make('products_sum_price', 'Sum');

    expect($stat)
        ->toBeInstanceOf(Sum::class)
        ->getName()->toBe('products_sum_price')
        ->getLabel()->toBe('Sum')
        ->getValue()->toBeInstanceOf(Closure::class);
});

it('infers aggregate relationship', function () {
    $stat = Sum::make('products_sum_price', 'Sum');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->sum);
});

it('specifies aggregate column', function () {
    $stat = Stat::make('price', 'Sum')
        ->sum('products', 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->sum);
});

it('specifies array relationship', function () {
    $stat = Stat::make('price', 'Sum')
        ->sum([
            'products' => fn (Builder $query) => $query,
        ], 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->sum);
});

it('specifies array relationship with aliasing', function () {
    $stat = Stat::make('price', 'Sum')
        ->sum([
            'products as expensive_products' => fn (Builder $query) => $query,
        ], 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->sum);
});

it('throws exception when no column is not specified', function () {
    Stat::make('products', 'Sum')
        ->sum('products');
})->throws(InvalidArgumentException::class);
