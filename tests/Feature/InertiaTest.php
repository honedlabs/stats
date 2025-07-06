<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use Honed\Stats\Overview;
use Illuminate\Support\Facades\DB;
use Inertia\Support\Header;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\get;

beforeEach(function () {
    $this->user = User::factory()->create();

    Product::factory(10)
        ->for($this->user)
        ->create();
});

it('renders page', function () {
    get(route('users.show', $this->user))
        ->assertInertia(fn (Assert $assert) => $assert
            ->has('_values', 3)
            ->has('_values.0', fn (Assert $assert) => $assert
                ->where('name', 'fixed')
                ->where('label', 'Fixed')
                ->etc()
            )
            ->has('_values.1', fn (Assert $assert) => $assert
                ->where('name', 'products_count')
                ->where('label', 'Count')
                ->etc()
            )
            ->has('_values.2', fn (Assert $assert) => $assert
                ->where('name', 'products_sum_price')
                ->where('label', 'Sum')
                ->etc()
            )
            ->where('_stat_key', Overview::PROP)
            ->missing('fixed')
        );

    get(route('users.show', $this->user), [
        Header::PARTIAL_COMPONENT => 'Users/Show',
    ])->assertInertia(fn (Assert $assert) => $assert
        ->where('fixed', 100)
        ->where('products_count', DB::table('products')->count())
        ->where('products_sum_price', DB::table('products')->sum('price'))
    );
});
