<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(base_path('stubs'));
});

afterEach(function () {
    File::cleanDirectory(base_path('stubs'));
});

it('publishes stubs', function () {
    $this->artisan('vendor:publish', ['--tag' => 'stats-stubs'])
        ->assertSuccessful();
});
