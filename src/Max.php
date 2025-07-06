<?php

declare(strict_types=1);

namespace Honed\Stats;

class Max extends Stat
{
    /**
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->max();
    }
}
