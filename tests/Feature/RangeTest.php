<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use Honed\Stats\Range;
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

    $this->min = DB::table('products')
        ->where('user_id', $this->user->id)
        ->min('price');

    $this->range = $this->max - $this->min;
});

it('creates range stat', function () {
    $stat = Range::make('products_range_price', 'Range');

    expect($stat)
        ->toBeInstanceOf(Range::class)
        ->getName()->toBe('products_range_price')
        ->getLabel()->toBe('Range')
        ->getValue()->toBeInstanceOf(Closure::class);
});

it('infers aggregate relationship', function () {
    $stat = Range::make('products_range_price', 'Range');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->range);
});

it('specifies aggregate column', function () {
    $stat = Stat::make('price', 'Range')
        ->range('products', 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->range);
});

it('specifies array relationship', function () {
    $stat = Stat::make('price', 'Range')
        ->range([
            'products' => fn (Builder $query) => $query,
        ], 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->range);
})->skip();

it('specifies array relationship with aliasing', function () {
    $stat = Stat::make('price', 'Range')
        ->range([
            'products as expensive_products' => fn (Builder $query) => $query,
        ], 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->range);
})->skip();

it('throws exception when no column is not specified', function () {
    Stat::make('products', 'Range')
        ->range('products');
})->throws(InvalidArgumentException::class);
