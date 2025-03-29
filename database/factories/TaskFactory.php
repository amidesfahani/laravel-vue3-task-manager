<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'completed']);

        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => $status,
            'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'end_date' => $status === 'completed' ? fake()->dateTimeBetween('now', '+1 month') : null,
        ];
    }
}
