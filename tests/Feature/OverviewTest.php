<?php

declare(strict_types=1);

use App\Models\User;
use App\Overviews\UserOverview;
use Honed\Stats\Overview;
use Honed\Stats\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Inertia\DeferProp;
use Inertia\IgnoreFirstLoad;
use Inertia\LazyProp;

beforeEach(function () {
    $this->overview = Overview::make();

    $this->user = class_basename(User::class);
});

afterEach(function () {
    Overview::flushState();
});

it('makes with stats', function () {
    expect(Overview::make([Stat::make('orders')]))
        ->toBeInstanceOf(Overview::class)
        ->getStats()->toHaveCount(1);
});

it('resolves overview for model', function () {
    expect(Overview::resolveOverview($this->user))
        ->toBeString()
        ->toBe(UserOverview::class);
});

it('gets overview for model', function () {
    expect(Overview::overviewForModel($this->user))
        ->toBeInstanceOf(UserOverview::class);
});

it('uses guess callback', function () {
    Overview::guessOverviewNamesUsing(fn (string $className) => Str::of($className)->classBasename()->value().'Overview');

    expect(Overview::resolveOverview(User::class))
        ->toBe(class_basename(UserOverview::class));
});

it('sets namespace', function () {
    Overview::useNamespace('\App\\Stats\\');

    expect(Overview::resolveOverview(User::class))
        ->toBe(Str::of(UserOverview::class)
            ->classBasename()
            ->prepend('\\App\\Stats\\')
            ->value()
        );
});

it('gets pairs', function () {
    expect($this->overview)
        ->getPairs()
        ->scoped(fn ($array) => $array
            ->toBeArray()
            ->toBeEmpty()
        )
        ->stat(Stat::make('orders')->value(100))
        ->getPairs()
        ->scoped(fn ($array) => $array
            ->toBeArray()
            ->toHaveCount(1)
            ->{0}->toEqualCanonicalizing([
                'name' => 'orders',
                'label' => 'Orders',
                'attributes' => [],
            ])
        );
});

it('gets simple value from stat', function () {
    expect($this->overview)
        ->getValue(Stat::make('orders')->value(100))
        ->toBe(100);
});

it('gets record value from stat', function () {
    $user = User::factory()->create();

    expect($this->overview)
        ->record($user)->toBe($this->overview)
        ->getValue(Stat::make('orders')->value(
            fn ($record) => $record->id
        ))
        ->toBe($user->id);
});

it('has array representation', function () {
    expect($this->overview)
        ->toArray()
        ->scoped(fn ($array) => $array
            ->toBeArray()
            ->toHaveKeys([
                '_values',
                '_stat_key',
            ])
            ->{'_values'}->toBe([])
            ->{'_stat_key'}->toBe(Overview::PROP)
        )
        ->stat(Stat::make('orders')->value(100))
        ->toArray()
        ->scoped(fn ($array) => $array
            ->toBeArray()
            ->toHaveKeys([
                '_values',
                '_stat_key',
                'orders',
            ])
            ->{'_values'}
            ->scoped(fn ($values) => $values
                ->toHaveCount(1)
                ->{0}->toEqualCanonicalizing([
                    'name' => 'orders',
                    'label' => 'Orders',
                    'attributes' => [],
                ])
            )
            ->{'_stat_key'}->toBe(Overview::PROP)
            ->{'orders'}->toBeInstanceOf(IgnoreFirstLoad::class)
        );
});

describe('loading strategies', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();

        $this->overview = $this->user->stats();
    });

    it('uses defer loading', function () {
        expect($this->overview)
            ->defer()->toBe($this->overview)
            ->toArray()
            ->scoped(fn ($array) => $array
                ->toBeArray()
                ->toHaveKeys(['_values', '_stat_key', 'fixed', 'products_count', 'products_sum_price'])
                ->{'fixed'}->toBeInstanceOf(DeferProp::class)
                ->{'products_count'}->toBeInstanceOf(DeferProp::class)
                ->{'products_sum_price'}->toBeInstanceOf(DeferProp::class)
            );
    })->skip(fn () => ! class_exists(DeferProp::class));

    it('uses lazy loading', function () {
        expect($this->overview)
            ->lazy()->toBe($this->overview)
            ->toArray()
            ->scoped(fn ($array) => $array
                ->toBeArray()
                ->toHaveKeys(['_values', '_stat_key', 'fixed', 'products_count', 'products_sum_price'])
                ->{'fixed'}->toBeInstanceOf(LazyProp::class)
                ->{'products_count'}->toBeInstanceOf(LazyProp::class)
                ->{'products_sum_price'}->toBeInstanceOf(LazyProp::class)
            );
    });

    it('groups lazy loading props', function () {
        expect($this->overview)
            ->lazy()->toBe($this->overview)
            ->group()->toBe($this->overview)
            ->toArray()
            ->scoped(fn ($array) => $array
                ->toBeArray()
                ->toHaveKeys(['_values', '_stat_key'])
                ->toHaveKey(Overview::PROP)
                ->{'_stat_key'}->toBe(Overview::PROP)
                ->{Overview::PROP}->toBeInstanceOf(LazyProp::class)
            );
    });
});

describe('evaluation', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();

        $this->overview = $this->user->stats();
    });

    it('has named dependencies', function ($closure, $class) {
        expect($this->overview->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        'model' => fn () => [fn ($model) => $model, User::class],
        'record' => fn () => [fn ($record) => $record, User::class],
        'row' => fn () => [fn ($row) => $row, User::class]
    ]);

    it('has typed dependencies', function ($closure, $class) {
        expect($this->overview->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        'model' => fn () => [fn (User $arg) => $arg, User::class],
        'class' => fn () => [fn (Model $arg) => $arg, Model::class],
    ]);
});

it('is macroable', function () {
    Overview::macro('test', function () {
        return 'test';
    });

    expect($this->overview)
        ->test()->toBe('test');
});
