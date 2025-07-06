<?php

declare(strict_types=1);

namespace Honed\Stats\Concerns;

use Closure;

trait CanHaveDescription
{
    /**
     * The description of the instance.
     *
     * @var string|(Closure():string)|null
     */
    protected $description;

    /**
     * Set the description of the instance.
     *
     * @param  string|(Closure():string)|null  $description
     * @return $this
     */
    public function description(string|Closure|null $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description of the instance.
     */
    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
    }
}
