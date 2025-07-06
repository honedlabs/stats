<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Overviews'));
});

afterEach(function () {
    File::cleanDirectory(app_path('Overviews'));
});

it('makes', function () {
    $this->artisan('make:overview', [
        'name' => 'UserOverview',
        '--force' => true,
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Overviews/UserOverview.php'));
});

it('bindings for a name', function () {
    $this->artisan('make:overview', [
        '--force' => true,
    ])->expectsQuestion('What should the overview be named?', 'UserOverview')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Overviews/UserOverview.php'));
});
