<?php

declare(strict_types=1);

namespace App\Responses;

use Honed\Stats\Concerns\Statistical;
use Honed\Stats\Contracts\DisplaysStats;

class IndexUserResponse implements DisplaysStats
{
    use Statistical;
}
