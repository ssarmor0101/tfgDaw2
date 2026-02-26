<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            UserSeeder::class
        ]);

        if (app()->environment('local')) {
            // Sólo se ejecutan estos seeders en el entorno local (desarrollo)
            $this->call([
                JuegoSeeder::class,
                LogroSeeder::class,
                ResultadoSeeder::class,
                PuntuacionSeeder::class,
                AmigoSeeder::class
            ]);
        }
    }
}
