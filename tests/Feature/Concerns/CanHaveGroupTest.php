<?php

declare(strict_types=1);

use Honed\Stats\Stat;

beforeEach(function () {
    $this->stat = Stat::make('users');
});

it('can have group', function () {
    expect($this->stat)
        ->getGroup()->toBeNull()
        ->group('test')->toBe($this->stat)
        ->getGroup()->toBe('test')
        ->dontGroup()->toBe($this->stat)
        ->getGroup()->toBeNull();
});
