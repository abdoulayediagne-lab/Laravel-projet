<?php

namespace Database\Factories;

use App\Models\Character;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => fake()->unique()->firstName(),
            'slug'        => fake()->unique()->slug(2),
            'rarity'      => 'normal',
            'probability' => 10,
            'color'       => fake()->hexColor(),
            'emoji'       => '🙂',
            'is_base'     => false,
        ];
    }

    public function base(): static
    {
        return $this->state(fn () => [
            'rarity'      => 'base',
            'probability' => 0,
            'is_base'     => true,
        ]);
    }

    public function legendary(): static
    {
        return $this->state(fn () => [
            'rarity'      => 'legendary',
            'probability' => 5,
            'is_base'     => false,
        ]);
    }
}
