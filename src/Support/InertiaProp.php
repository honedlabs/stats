<?php

declare(strict_types=1);

namespace Honed\Stats\Support;

use Inertia\Inertia;
use Inertia\OptionalProp;

final class InertiaProp
{
    /**
     * Create an optional Inertia prop (formerly known as lazy in Inertia v1/v2).
     */
    public static function optional(callable $callback): OptionalProp
    {
        if (class_exists(OptionalProp::class)) {
            return Inertia::optional($callback);
        }

        /** @phpstan-ignore staticMethod.notFound (Inertia v1/v2) */
        return Inertia::lazy($callback);
    }

    /**
     * Get the optional/lazy prop class for the installed Inertia version.
     *
     * @return class-string
     */
    public static function optionalClass(): string
    {
        if (class_exists(OptionalProp::class)) {
            return OptionalProp::class;
        }

        return 'Inertia\LazyProp'; // @phpstan-ignore-line return.type
    }
}
