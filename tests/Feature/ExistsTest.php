<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use Honed\Stats\Exists;
use Honed\Stats\Stat;
use Illuminate\Database\Eloquent\Builder;

beforeEach(function () {
    $this->user = User::factory()->create();

    Product::factory(10)
        ->for($this->user)
        ->create();
});

it('creates count stat', function () {
    $stat = Exists::make('products_exists', 'Exists');

    expect($stat)
        ->toBeInstanceOf(Exists::class)
        ->getName()->toBe('products_exists')
        ->getLabel()->toBe('Exists')
        ->getValue()->toBeInstanceOf(Closure::class);
});

it('infers aggregate relationship', function () {
    $stat = Exists::make('products_exists', 'Exists');

    $value = $stat->getValue()($this->user);

    expect($value)->toBeTrue();
});

it('specifies aggregate column', function () {
    $stat = Stat::make('products', 'Exists')
        ->exists('products');

    $value = $stat->getValue()($this->user);

    expect($value)->toBeTrue();
});

it('specifies array relationship', function () {
    $stat = Stat::make('products', 'Exists')
        ->exists([
            'products' => fn (Builder $query) => $query,
        ]);

    $value = $stat->getValue()($this->user);

    expect($value)->toBeTrue();
});

it('specifies array relationship with aliasing', function () {
    $stat = Stat::make('products', 'Exists')
        ->exists([
            'products as products_created' => fn (Builder $query) => $query,
        ]);

    $value = $stat->getValue()($this->user);

    expect($value)->toBeTrue();
});
