<?php

declare(strict_types=1);

namespace Honed\Stats\Concerns;

use Honed\Stats\Attributes\UseOverview;
use Honed\Stats\Overview;
use ReflectionClass;

/**
 * @template TOverview of \Honed\Stats\Overview
 *
 * @property-read class-string<TOverview> $overviewClass The overview for this model.
 */
trait HasOverview
{
    /**
     * Get the overview instance for the model.
     *
     * @return TOverview
     */
    public static function overview()
    {
        return static::newOverview()
            ?? Overview::overviewForModel(static::class);

    }

    /**
     * Get the overview instance for the model and bind the current model to it.
     *
     * @return TOverview
     */
    public function stats()
    {
        return $this->overview()->record($this);
    }

    /**
     * Create a new table instance for the model.
     *
     * @return TOverview|null
     */
    protected static function newOverview(): ?Overview
    {
        return match (true) {
            isset(static::$overviewClass) => static::$overviewClass::make(),
            (bool) $overview = static::getUseOverviewAttribute() => $overview::make(),
            default => null,
        };
    }

    /**
     * Get the overview from the UseOverview class attribute.
     *
     * @return class-string<Overview>|null
     */
    protected static function getUseOverviewAttribute(): ?string
    {
        $attributes = (new ReflectionClass(static::class))
            ->getAttributes(UseOverview::class);

        if ($attributes !== []) {
            $useOverview = $attributes[0]->newInstance();

            return $useOverview->overviewClass;
        }

        return null;
    }
}
