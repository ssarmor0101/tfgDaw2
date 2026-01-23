<?php

namespace Database\Factories;

use App\Models\Juego;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Puntuacion>
 */
class PuntuacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => User::inRandomOrder()->first()->id,
            "Juego_id" => Juego::inRandomOrder()->first()->id,
            "puntuacion" => fake()->numerify('####00')
        ];
    }
}
