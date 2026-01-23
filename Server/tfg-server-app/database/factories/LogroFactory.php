<?php

namespace Database\Factories;

use App\Models\Juego;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Logro>
 */
class LogroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "juego_id" => Juego::inRandomOrder()->first()->id,
            "name" => fake()->sentence(1),
            "description" => fake()->sentence()
        ];
    }
}
