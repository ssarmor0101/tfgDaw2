<?php

namespace Database\Seeders;

use App\Models\Amigo;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function Symfony\Component\Clock\now;

class AmigoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::pluck('id')->toArray();

        $pares = [];

        for ($i = 0; $i < count($users); $i++) {
            for ($j = $i + 1; $j < count($users); $j++) {
                $pares[] = [
                    'user_id' => $users[$i],
                    'friend_id' => $users[$j],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        shuffle($pares);

        Amigo::insert(array_slice($pares, 0, intdiv(count($users), 2)));
    }
}
