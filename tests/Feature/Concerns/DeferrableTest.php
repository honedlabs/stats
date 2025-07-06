<?php

declare(strict_types=1);

use Honed\Stats\Overview;

beforeEach(function () {
    $this->overview = Overview::make();
});

it('can defer', function () {
    expect($this->overview)
        ->getDeferStrategy()->toBe('defer')
        ->defer('lazy')->toBe($this->overview)
        ->getDeferStrategy()->toBe('lazy');
});

it('can be lazy', function () {
    expect($this->overview)
        ->isLazy()->toBeFalse()
        ->lazy()->toBe($this->overview)
        ->isLazy()->toBeTrue();
});

it('can be defer', function () {
    expect($this->overview)
        ->isDefer()->toBeTrue();
});
