<?php

namespace Database\Seeders;

use App\Models\Amigo;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmigoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $amigosPosibles = (User::all()->count())/2;
        // Amigo::factory()->count($amigosPosibles)->create();

        // ------------------------------------------------------

        // $max = User::count();
        // $objetivo = intdiv($max, 2);

        // $creados = 0;

        // while ($creados < $objetivo) {
        //     try {
        //         Amigo::factory()->create();
        //         $creados++;
        //     } catch (\Illuminate\Database\QueryException $e) {
        //         // Duplicado → ignoramos y seguimos
        //     }
        // }

        // -----------------------------------------------------

        $users = User::pluck('id')->toArray();

        $pares = [];

        for ($i = 0; $i < count($users); $i++) {
            for ($j = $i + 1; $j < count($users); $j++) {
                $pares[] = [
                    'user_id' => $users[$i],
                    'friend_id' => $users[$j],
                ];
            }
        }

        shuffle($pares);

        Amigo::insert(array_slice($pares, 0, intdiv(count($users), 2)));
    }
}
