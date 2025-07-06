<?php

declare(strict_types=1);

use Illuminate\Console\Command;

arch()->preset()->php();

arch()->preset()->security();

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('strict types')
    ->expect('Honed\Stats')
    ->toUseStrictTypes();

arch('concerns')
    ->expect('Honed\Stats\Concerns')
    ->toBeTraits();

arch('contracts')
    ->expect('Honed\Stats\Contracts')
    ->toBeInterfaces();

arch('commands')
    ->expect('Honed\Stats\Commands')
    ->toBeClasses()
    ->toExtend(Command::class);
