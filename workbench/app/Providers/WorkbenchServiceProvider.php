<?php

declare(strict_types=1);

namespace App\Providers;

use Honed\Stats\Overview;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use function Orchestra\Testbench\workbench_path;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        View::addLocation(workbench_path('resources/views'));

        config()->set('inertia.testing.ensure_pages_exist', false);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Use lazy loading for overviews by default due to defer requiring Inertia 2.0.
        Overview::configureUsing(fn (Overview $overview) => $overview
            ->lazy()
        );
    }
}
