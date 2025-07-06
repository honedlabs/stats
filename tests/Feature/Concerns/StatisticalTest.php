<?php

declare(strict_types=1);

use App\Responses\IndexUserResponse;
use Honed\Stats\Overview;
use Honed\Stats\Stat;
use Inertia\IgnoreFirstLoad;
use Inertia\LazyProp;

beforeEach(function () {
    $this->response = new IndexUserResponse();
});

it('adds stats to overview', function () {
    expect($this->response)
        ->getOverview()
        ->scoped(fn ($overview) => $overview
            ->toBeInstanceOf(Overview::class)
            ->getStats()->toBeEmpty()
        )
        ->stats(Stat::make('users'))->toBe($this->response)
        ->getOverview()
        ->scoped(fn ($overview) => $overview
            ->toBeInstanceOf(Overview::class)
            ->getStats()
            ->scoped(fn ($stats) => $stats
                ->toBeArray()
                ->toHaveCount(1)
                ->{0}
                ->scoped(fn ($stat) => $stat
                    ->toBeInstanceOf(Stat::class)
                    ->getName()->toBe('users')
                )
            )
        );
});

it('overwrites overview', function () {
    expect($this->response)
        ->getOverview()
        ->scoped(fn ($overview) => $overview
            ->toBeInstanceOf(Overview::class)
            ->getStats()->toBeEmpty()
        )
        ->overview(Overview::make([Stat::make('users')]))
        ->getOverview()
        ->scoped(fn ($overview) => $overview
            ->toBeInstanceOf(Overview::class)
            ->getStats()
            ->scoped(fn ($stats) => $stats
                ->toBeArray()
                ->toHaveCount(1)
                ->{0}
                ->scoped(fn ($stat) => $stat
                    ->toBeInstanceOf(Stat::class)
                    ->getName()->toBe('users')
                )
            )
        );
});

it('has overview props', function () {
    expect($this->response)
        ->overviewProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveKeys(['_values', '_stat_key'])
            ->{'_values'}->toBe([])
            ->{'_stat_key'}->toBe(Overview::PROP)
        )
        ->overview(Overview::make([Stat::make('users')]))
        ->overviewProps()
        ->scoped(fn ($props) => $props
            ->toBeArray()
            ->toHaveKeys(['_values', '_stat_key', 'users'])
            ->{'_values'}
            ->scoped(fn ($values) => $values
                ->toBeArray()
                ->toHaveCount(1)
                ->{0}
                ->scoped(fn ($value) => $value
                    ->toBeArray()
                    ->toHaveKeys(['value', 'label'])
                    ->{'value'}->toBe('users')
                    ->{'label'}->toBe('Users')
                )
            )
            ->{'_stat_key'}->toBe(Overview::PROP)
            ->{'users'}->toBeInstanceOf(LazyProp::class)
        );
});
