<?php

declare(strict_types=1);

namespace App\Overviews;

use Honed\Stats\Count;
use Honed\Stats\Overview;
use Honed\Stats\Stat;
use Honed\Stats\Sum;

class UserOverview extends Overview
{
    /**
     * Define the overview.
     *
     * @return $this
     */
    protected function definition(): static
    {
        return $this
            ->stats([
                Stat::make('fixed')
                    ->value(100),

                Count::make('products_count', 'Count'),

                Sum::make('products_sum_price', 'Sum'),
            ]);
    }
}
