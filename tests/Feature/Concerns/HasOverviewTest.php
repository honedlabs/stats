<?php

declare(strict_types=1);

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Overviews\OrderOverview;
use App\Overviews\ProductOverview;
use App\Overviews\UserOverview;
use Honed\Stats\Overview;

afterEach(function () {
    Overview::flushState();
});

it('gets overview from attribute', function () {
    expect(User::overview())
        ->toBeInstanceOf(UserOverview::class);
});

it('gets overview from static property', function () {
    expect(Product::overview())
        ->toBeInstanceOf(ProductOverview::class);
});

it('guesses overview from model name', function () {
    expect(Order::overview())
        ->toBeInstanceOf(OrderOverview::class);
});

it('binds model to overview', function () {
    $user = User::factory()->create();

    expect($user->stats())
        ->toBeInstanceOf(UserOverview::class)
        ->getRecord()->toBe($user);
});
