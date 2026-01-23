<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Amigo>
 */
class AmigoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::inRandomOrder()->take(2)->pluck('id');

        $userA = $users[0];
        $userB = $users[1];

        return [
            'user_id'   => min($userA, $userB),
            'friend_id' => max($userA, $userB),
        ];
    }
}
