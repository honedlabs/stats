<?php

declare(strict_types=1);

use Honed\Stats\Overview;

beforeEach(function () {
    $this->overview = Overview::make();
});

it('groups', function () {
    expect($this->overview)
        ->isGroup()->toBeFalse()
        ->group()->toBe($this->overview)
        ->isGroup()->toBeTrue();
});

it('does not group', function () {
    expect($this->overview)
        ->isNotGroup()->toBeTrue()
        ->dontGroup()->toBe($this->overview)
        ->isNotGroup()->toBeTrue();
});
