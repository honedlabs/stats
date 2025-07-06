<?php

declare(strict_types=1);

namespace Honed\Stats\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseOverview
{
    /**
     * Create a new attribute instance.
     *
     * @param  class-string<\Honed\Stats\Overview>  $overviewClass
     */
    public function __construct(
        public string $overviewClass
    ) {}
}
