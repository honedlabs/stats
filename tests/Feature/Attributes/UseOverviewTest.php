<?php

declare(strict_types=1);

use App\Models\User;
use App\Overviews\UserOverview;
use Honed\Stats\Attributes\UseOverview;

it('has attribute', function () {
    $attribute = new UseOverview(UserOverview::class);

    expect($attribute)
        ->toBeInstanceOf(UseOverview::class)
        ->overviewClass->toBe(UserOverview::class);

    expect(User::class)
        ->toHaveAttribute(UseOverview::class);
});
