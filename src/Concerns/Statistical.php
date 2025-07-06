<?php

declare(strict_types=1);

namespace Honed\Stats\Concerns;

use Honed\Stats\Overview;
use Honed\Stats\Stat;

/**
 * @phpstan-require-implements \Honed\Stats\Contracts\DisplaysStats
 */
trait Statistical
{
    /**
     * The stat overview.
     *
     * @var Overview|null
     */
    protected $overview;

    /**
     * Set a profile to use for the stats.
     *
     * @return $this
     */
    public function overview(Overview $overview): static
    {
        $this->overview = $overview;

        return $this;
    }

    /**
     * Set stats to use for the profile.
     */
    public function stats(array|Stat $stats): static
    {
        /** @var array<int,Stat> */
        $stats = is_array($stats) ? $stats : func_get_args();

        $this->getOverview()->stats($stats);

        return $this;
    }

    /**
     * Get the profile.
     */
    public function getOverview(): Overview
    {
        return $this->overview ??= Overview::make();
    }

    /**
     * Get the overview as a props array for spreading.
     *
     * @return array<string,mixed>
     */
    public function overviewProps(): array
    {
        return $this->getOverview()->toArray();
    }
}
