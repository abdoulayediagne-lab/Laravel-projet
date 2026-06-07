<?php

namespace Database\Factories;

use App\Models\Score;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Score>
 */
class ScoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'         => User::factory(),
            'character_id'    => null,
            'score'           => fake()->numberBetween(0, 5000),
            'coins_collected' => fake()->numberBetween(0, 200),
            'difficulty'      => fake()->randomElement(['normal', 'hard']),
            'duration'        => fake()->numberBetween(10, 300),
        ];
    }
}
