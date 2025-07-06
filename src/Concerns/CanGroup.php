<?php

declare(strict_types=1);

namespace Honed\Stats\Concerns;

trait CanGroup
{
    /**
     * Whether to group the data.
     *
     * @var bool
     */
    protected $group = false;

    /**
     * Set whether to group the data.
     *
     * @return $this
     */
    public function group(bool $value = true): static
    {
        $this->group = $value;

        return $this;
    }

    /**
     * Set whether to not group the data.
     *
     * @return $this
     */
    public function dontgroup(bool $value = true): static
    {
        return $this->group(! $value);
    }

    /**
     * Determine whether the data is group.
     */
    public function isGroup(): bool
    {
        return $this->group;
    }

    /**
     * Determine whether the data is not group.
     */
    public function isNotGroup(): bool
    {
        return ! $this->isGroup();
    }
}
