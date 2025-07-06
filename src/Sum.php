<?php

declare(strict_types=1);

namespace Honed\Stats;

class Sum extends Stat
{
    /**
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->sum();
    }
}
