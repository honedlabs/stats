<?php

declare(strict_types=1);

namespace Honed\Stats;

use Closure;
use Honed\Core\Concerns\HasAttributes;
use Honed\Core\Concerns\HasDescription;
use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasName;
use Honed\Core\Contracts\NullsAsUndefined;
use Honed\Core\Primitive;
use Honed\Stats\Concerns\HasGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @extends \Honed\Core\Primitive<string, mixed>
 */
class Stat extends Primitive implements NullsAsUndefined
{
    use HasAttributes;
    use HasDescription;
    use HasGroup;
    use HasIcon;
    use HasLabel;
    use HasName;

    /**
     * The value of the stat.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Create a new stat instance.
     */
    public static function make(string $name, ?string $label = null): static
    {
        return resolve(static::class)
            ->name($name)
            ->label($label ?? static::makeLabel($name));
    }

    /**
     * Set the value of the stat.
     *
     * @return $this
     */
    public function value(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of the stat.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Set the value of the stat to be retrieved from a count of a relationship.
     *
     * @param  string|array{string, Closure}|null  $relationship
     * @return $this
     */
    public function count(string|array|null $relationship = null): static
    {
        return $this->newSimpleRelationship($relationship, 'count');
    }

    /**
     * Set the value of the stat to be retrieved from a relationship exists.
     *
     * @param  string|array{string, Closure}|null  $relationship
     * @return $this
     */
    public function exists(string|array|null $relationship = null): static
    {
        return $this->newSimpleRelationship($relationship, 'exists');
    }

    /**
     * Set the value of the stat to be retrieved from an average value of a relationship.
     *
     * @param  string|array{string, Closure}|null  $relationship
     * @return $this
     */
    public function avg(string|array|null $relationship = null, ?string $column = null): static
    {
        return $this->newAggregateRelationship($relationship, $column, 'avg');
    }

    /**
     * Set the value of the stat to be retrieved from an average value of a relationship.
     *
     * @param  string|array{string, Closure}|null  $relationship
     * @return $this
     */
    public function average(string|array|null $relationship = null, ?string $column = null): static
    {
        return $this->avg($relationship, $column);
    }

    /**
     * Set the value of the stat to be retrieved from a sum of a relationship.
     *
     * @param  string|array{string, Closure}|null  $relationship
     * @return $this
     */
    public function sum(string|array|null $relationship = null, ?string $column = null): static
    {
        return $this->newAggregateRelationship($relationship, $column, 'sum');
    }

    /**
     * Set the value of the stat to be retrieved from a maximum value of a relationship.
     *
     * @param  string|array{string, Closure}|null  $relationship
     * @return $this
     */
    public function max(string|array|null $relationship = null, ?string $column = null): static
    {
        return $this->newAggregateRelationship($relationship, $column, 'max');
    }

    /**
     * Set the value of the stat to be retrieved from a minimum value of a relationship.
     *
     * @param  string|array{string, Closure}|null  $relationship
     * @return $this
     */
    public function min(string|array|null $relationship = null, ?string $column = null): static
    {
        return $this->newAggregateRelationship($relationship, $column, 'min');
    }

    /**
     * Set the value of the stat to be retrieved from a range (max - min) of a relationship.
     *
     * @param  string|array{string, Closure}|null  $relationship
     * @return $this
     */
    public function range(string|array|null $relationship = null, ?string $column = null): static
    {
        if ($relationship && ! $column) {
            $this->throwInvalidAggregateCall();
        }

        return $this->value(match (true) {
            (bool) $relationship => fn (Model $record) => $this->loadRange($record, $relationship, $column),
            default => fn (Model $record) => $this->loadRange(
                $record,
                Str::beforeLast($this->getName(), '_range'),
                Str::afterLast($this->getName(), 'range_'),
            ),
        });
    }

    /**
     * Get the name of the load method.
     */
    protected function load(string $method): string
    {
        return 'load'.Str::studly($method);
    }

    /**
     * Get the name of the attribute to be retrieved from an aggregate relationship.
     *
     * @param  string|array{string, Closure}|null  $relationship
     */
    protected function getAttributeName(string|array|null $relationship, string $method, ?string $column = null): string
    {
        if (! $relationship) {
            return $this->getName();
        }

        if (is_array($relationship)) {
            /** @var string */
            $relationship = array_key_first($relationship);
        }

        if (Str::contains($relationship, ' as ')) {
            return Str::afterLast($relationship, ' as ');
        }

        return implode('_', array_values(
            array_filter([
                $relationship,
                $method,
                $column,
            ])
        ));
    }

    /**
     * Guess the name of the relationship.
     *
     * @param  string|array{string, Closure}|null  $relationship
     * @return string|array{string, Closure}
     */
    protected function getRelationship(string|array|null $relationship, string $method): string|array
    {
        return match (true) {
            (bool) $relationship => $relationship,
            default => Str::beforeLast($this->getName(), '_'.$method),
        };
    }

    /**
     * Guess the name of the aggregating column.
     */
    protected function getColumnName(?string $column, string $method): string
    {
        return $column ?? Str::afterLast($this->getName(), $method.'_');
    }

    /**
     * Calculate the range (max - min) for a relationship.
     *
     * @param  string|array{string, Closure}  $relationship
     */
    protected function loadRange(Model $record, string|array $relationship, string $column): float|int|null
    {
        $query = $record->{$relationship}();

        $max = $query->max($column);
        $min = $query->min($column);

        if ($max === null || $min === null) {
            return null;
        }

        return $max - $min;
    }

    /**
     * Set the value of the stat to be retrieved from a simple relationship.
     *
     * @param  string|array{string, Closure}|null  $relationship
     * @return $this
     */
    protected function newSimpleRelationship(string|array|null $relationship, string $method): static
    {
        return $this->value(
            fn (Model $record) => $record->{$this->load($method)}(
                $this->getRelationship($relationship, $method)
            )->{$this->getAttributeName($relationship, $method)}
        );
    }

    /**
     * Set the value of the stat to be retrieved from an aggregate relationship.
     *
     * @param  string|array{string, Closure}|null  $relationship
     * @return $this
     */
    protected function newAggregateRelationship(string|array|null $relationship, ?string $column, string $method): static
    {
        if ($this->invalidAggregateCall($relationship, $column)) {
            $this->throwInvalidAggregateCall();
        }

        return $this->value(fn (Model $record) => $record->{$this->load($method)}(
            $this->getRelationship($relationship, $method),
            $this->getColumnName($column, $method),
        )->{$this->getAttributeName($relationship, $method, $column)});
    }

    /**
     * Determine if the aggregate call is invalid.
     *
     * @param  string|array{string, Closure}|null  $relationship
     */
    protected function invalidAggregateCall(string|array|null $relationship, ?string $column): bool
    {
        return $relationship && ! $column && ! is_array($relationship);
    }

    /**
     * Throw an invalid aggregate call exception.
     *
     * @throws InvalidArgumentException
     */
    protected function throwInvalidAggregateCall(): never
    {
        throw new InvalidArgumentException(
            'A column must be specified when an aggregate relationship is used.'
        );
    }

    /**
     * Define the stat.
     *
     * @return $this
     */
    protected function definition(): static
    {
        return $this;
    }

    /**
     * Get the representation of the stat.
     *
     * @return array<string, mixed>
     */
    protected function representation(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'icon' => $this->getIcon(),
            'description' => $this->getDescription(),
            'attributes' => $this->getAttributes(),
        ];
    }
}
