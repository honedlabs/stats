<?php

declare(strict_types=1);

use Honed\Stats\Stat;

beforeEach(function () {
    $this->stat = Stat::make('name');
});

it('has name and label', function () {
    expect($this->stat)
        ->getName()->toBe('name')
        ->getLabel()->toBe('Name');

    expect(Stat::make('name', 'Label'))
        ->getName()->toBe('name')
        ->getLabel()->toBe('Label');
});

it('has value', function () {
    expect($this->stat)
        ->getValue()->toBeNull()
        ->value('test')->toBe($this->stat)
        ->getValue()->toBe('test')
        ->value(fn () => 'callback')->toBe($this->stat)
        ->getValue()->toBeInstanceOf(Closure::class);
});

it('has array representation', function () {
    expect($this->stat)
        ->toArray()
        ->scoped(fn ($array) => $array
            ->toBeArray()
            ->toHaveCount(3)
            ->toHaveKeys(['name', 'label', 'attributes'])
            ->{'name'}->toBe('name')
            ->{'label'}->toBe('Name')
            ->{'attributes'}->toBe([])
        )
        ->icon('Plus')->toBe($this->stat)
        ->attributes(['class' => 'bg-red-500'])->toBe($this->stat)
        ->description('test')->toBe($this->stat)
        ->toArray()
        ->scoped(fn ($array) => $array
            ->toBeArray()
            ->toHaveCount(5)
            ->toHaveKeys(['name', 'label', 'attributes', 'description', 'icon'])
            ->{'name'}->toBe('name')
            ->{'label'}->toBe('Name')
            ->{'attributes'}->toBe(['class' => 'bg-red-500'])
            ->{'description'}->toBe('test')
            ->{'icon'}->toBe('Plus')
        );
});
