<?php

declare(strict_types=1);

use Honed\Stats\Stat;

beforeEach(function () {
    $this->stat = Stat::make('test');
});

it('can have description', function () {
    expect($this->stat)
        ->getDescription()->toBeNull()
        ->description('test')->toBe($this->stat)
        ->getDescription()->toBe('test')
        ->description(fn () => 'callback')->toBe($this->stat)
        ->getDescription()->toBe('callback');
});
