<?php

namespace Database\Factories;

use App\Models\Widget;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Widget>
 */
class WidgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true) . ' Widget',
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 5, 500),
            'quantity' => fake()->numberBetween(0, 1000),
            'status' => fake()->randomElement(['active', 'inactive', 'archived']),
            'metadata' => [
                'color' => fake()->colorName(),
                'size' => fake()->randomElement(['small', 'medium', 'large']),
                'weight' => fake()->randomFloat(2, 0.1, 10),
            ],
        ];
    }

    /**
     * Indicate that the widget is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the widget is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the widget is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }
}

