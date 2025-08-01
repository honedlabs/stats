<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Status;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @template TModel of \App\Models\Product
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_id' => Str::uuid()->toString(),
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->numberBetween(100, 1000),
            'best_seller' => fake()->boolean(),
            'status' => fake()->randomElement(Status::cases()),
            'created_at' => now()->subDays(fake()->numberBetween(16, 30)),
            'updated_at' => now()->subDays(fake()->numberBetween(1, 15)),
        ];
    }
}
