<?php

declare(strict_types=1);

namespace Honed\Stats\Contracts;

use Honed\Stats\Overview;

interface DisplaysStats
{
    /**
     * Get the overview.
     */
    public function getOverview(): Overview;

    /**
     * Get the overview as a props array for spreading.
     *
     * @return array<string,mixed>
     */
    public function overviewProps(): array;
}
