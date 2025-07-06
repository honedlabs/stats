<?php

declare(strict_types=1);

namespace Honed\Stats;

use Closure;
use Honed\Core\Concerns\HasRecord;
use Honed\Core\Primitive;
use Honed\Stats\Concerns\CanGroup;
use Honed\Stats\Concerns\Deferrable;
use Honed\Stats\Concerns\HasStats;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inertia\DeferProp;
use Inertia\IgnoreFirstLoad;
use Inertia\Inertia;
use Inertia\LazyProp;
use Throwable;

/**
 * @extends \Honed\Core\Primitive<string, mixed>
 */
class Overview extends Primitive
{
    use CanGroup;
    use Deferrable;
    use HasRecord;
    use HasStats;

    public const PROP = 'stats';

    /**
     * The default namespace where overviews reside.
     *
     * @var string
     */
    public static $namespace = 'App\\Overviews\\';

    /**
     * The identifier to use for evaluation.
     *
     * @var string
     */
    protected $evaluationIdentifier = 'overview';

    /**
     * How to resolve the overview for the given model name.
     *
     * @var (Closure(class-string<Model>):class-string<Overview>)|null
     */
    protected static $overviewResolver;

    /**
     * Create a new profile instance.
     *
     * @param  array<int,Stat>|Stat  $stats
     */
    public static function make(array|Stat $stats = []): static
    {
        return resolve(static::class)->stats($stats);
    }

    /**
     * Get a new table instance for the given model name.
     *
     * @param  class-string<Model>  $modelName
     */
    public static function overviewForModel(string $modelName): self
    {
        $overview = static::resolveOverview($modelName);

        return $overview::make();
    }

    /**
     * Get the overview name for the given model name.
     *
     * @param  class-string<Model>  $className
     * @return class-string<Overview>
     */
    public static function resolveOverview(string $className): string
    {
        $resolver = static::$overviewResolver ?? function (string $className) {
            $appNamespace = static::appNamespace();

            $className = Str::startsWith($className, $appNamespace.'Models\\')
                ? Str::after($className, $appNamespace.'Models\\')
                : Str::after($className, $appNamespace);

            /** @var class-string<Overview> */
            return static::$namespace.$className.'Overview';
        };

        return $resolver($className);
    }

    /**
     * Specify the default namespace that contains the application's model overviews.
     */
    public static function useNamespace(string $namespace): void
    {
        static::$namespace = $namespace;
    }

    /**
     * Specify the callback that should be invoked to guess the name of a model table.
     *
     * @param  Closure(class-string<Model>):class-string<Overview>  $callback
     */
    public static function guessOverviewNamesUsing(Closure $callback): void
    {
        static::$overviewResolver = $callback;
    }

    /**
     * Flush the global configuration state.
     */
    public static function flushState(): void
    {
        static::$overviewResolver = null;
        static::$namespace = 'App\\Overviews\\';
    }

    /**
     * Get the values of the stats.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPairs(): array
    {
        return array_map(
            static fn (Stat $stat) => $stat->toArray(),
            $this->getStats()
        );
    }

    /**
     * Get the value from a stat.
     */
    public function getValue(Stat $stat): mixed
    {
        return $this->evaluate($stat->getValue());
    }

    /**
     * Get the application namespace for the application.
     */
    protected static function appNamespace(): string
    {
        try {
            return Container::getInstance()
                ->make(Application::class)
                ->getNamespace();
        } catch (Throwable) {
            return 'App\\';
        }
    }

    /**
     * Define the profile.
     *
     * @return $this
     */
    protected function definition(): static
    {
        return $this;
    }

    /**
     * Get the representation of the instance.
     *
     * @return array<string,mixed>
     */
    protected function representation(): array
    {
        return [
            '_values' => $this->getPairs(),
            '_stat_key' => self::PROP,
            ...$this->getProps(),
        ];
    }

    /**
     * Get the key of the stats.
     */
    protected function getStatKey(): ?string
    {
        if ($this->isGroup() && $this->isLazy()) {
            return self::PROP;
        }

        return null;
    }

    /**
     * Get the stats.
     *
     * @return array<string, LazyProp|DeferProp>
     */
    protected function getProps(): array
    {
        $stats = $this->getStats();

        if ($key = $this->getStatKey()) {
            return [
                $key => Inertia::lazy(fn () => Arr::mapWithKeys(
                    $stats,
                    fn (Stat $stat) => [
                        $stat->getName() => $this->getValue($stat),
                    ]
                )),
            ];
        }

        return Arr::mapWithKeys(
            $stats,
            fn (Stat $stat) => [
                $stat->getName() => $this->newProp($stat),
            ]
        );
    }

    /**
     * Create the deferred newProp.
     */
    protected function newProp(Stat $stat): LazyProp|DeferProp
    {
        $callback = fn () => $this->getValue($stat);

        return match (true) {
            $this->isLazy() => Inertia::lazy($callback),
            default => Inertia::defer($callback, $stat->getGroup() ?? 'default'),
        };
    }

    /**
     * Provide a selection of default dependencies for evaluation by name.
     *
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'model', 'record', 'row' => [$this->getRecord()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * Provide a selection of default dependencies for evaluation by type.
     *
     * @param  class-string  $parameterType
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getRecord();

        if (! $record instanceof Model) {
            return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
        }

        return match ($parameterType) {
            self::class => [$this],
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
