<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Stats'));
});

afterEach(function () {
    File::cleanDirectory(app_path('Stats'));
});

it('makes', function () {
    $this->artisan('make:stat', [
        'name' => 'OrderCount',
        '--force' => true,
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Stats/OrderCount.php'));
});

it('bindings for a name', function () {
    $this->artisan('make:stat', [
        '--force' => true,
    ])->expectsQuestion('What should the stat be named?', 'OrderCount')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Stats/OrderCount.php'));
});
