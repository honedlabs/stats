<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use Honed\Stats\Count;
use Honed\Stats\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->user = User::factory()->create();

    Product::factory(10)
        ->for($this->user)
        ->create();

    $this->count = DB::table('products')
        ->where('user_id', $this->user->id)
        ->count();
});

it('creates count stat', function () {
    $stat = Count::make('products_count', 'Count');

    expect($stat)
        ->toBeInstanceOf(Count::class)
        ->getName()->toBe('products_count')
        ->getLabel()->toBe('Count')
        ->getValue()->toBeInstanceOf(Closure::class);
});

it('infers aggregate relationship', function () {
    $stat = Count::make('products_count', 'Count');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->count);
});

it('specifies aggregate column', function () {
    $stat = Stat::make('price', 'Count')
        ->count('products');

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->count);
});

it('specifies array relationship', function () {
    $stat = Stat::make('price', 'Count')
        ->count([
            'products' => fn (Builder $query) => $query,
        ]);

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->count);
});

it('specifies array relationship with aliasing', function () {
    $stat = Stat::make('price', 'Count')
        ->count([
            'products as expensive_products' => fn (Builder $query) => $query,
        ]);

    $value = $stat->getValue()($this->user);

    expect($value)->toBe($this->count);
});
