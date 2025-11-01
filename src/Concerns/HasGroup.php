<?php

declare(strict_types=1);

namespace Honed\Stats\Concerns;

trait HasGroup
{
    /**
     * The group for the instance.
     *
     * @var string|null
     */
    protected $group;

    /**
     * Set the group for the instance.
     *
     * @return $this
     */
    public function group(?string $group): static
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Set there to be no group.
     *
     * @return $this
     */
    public function dontGroup(): static
    {
        return $this->group(null);
    }

    /**
     * Get the group for the instance.
     */
    public function getGroup(): ?string
    {
        return $this->group;
    }
}
