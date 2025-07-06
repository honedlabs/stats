<?php

declare(strict_types=1);

namespace Honed\Stats;

use Honed\Stats\Commands\OverviewMakeCommand;
use Honed\Stats\Commands\StatMakeCommand;
use Illuminate\Support\ServiceProvider;

class StatsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->offerPublishing();

            $this->commands([
                StatMakeCommand::class,
                OverviewMakeCommand::class,
            ]);
        }
    }

    /**
     * Register the publishing for the package.
     */
    protected function offerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs'),
        ], 'stats-stubs');
    }
}
