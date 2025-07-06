<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use Honed\Stats\Average;
use Honed\Stats\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create();

    Product::factory(10)
        ->for($this->user)
        ->create();

    $this->average = DB::table('products')
        ->where('user_id', $this->user->id)
        ->avg('price');
});

it('creates average stat', function () {
    $stat = Average::make('products_avg_price', 'Average');

    expect($stat)
        ->toBeInstanceOf(Average::class)
        ->getName()->toBe('products_avg_price')
        ->getLabel()->toBe('Average')
        ->getValue()->toBeInstanceOf(Closure::class);
});

it('infers aggregate relationship', function () {
    $stat = Average::make('products_avg_price', 'Average');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->average);
});

it('specifies aggregate column', function () {
    $stat = Stat::make('price', 'Average')
        ->average('products', 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->average);
});

it('specifies array relationship', function () {
    $stat = Stat::make('price', 'Average')
        ->average([
            'products' => fn (Builder $query) => $query,
        ], 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->average);
});

it('specifies array relationship with aliasing', function () {
    $stat = Stat::make('price', 'Average')
        ->average([
            'products as expensive_products' => fn (Builder $query) => $query,
        ], 'price');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->average);
});

it('throws exception when no column is not specified', function () {
    $stat = Stat::make('products', 'Average')
        ->average('products');
})->throws(InvalidArgumentException::class);
