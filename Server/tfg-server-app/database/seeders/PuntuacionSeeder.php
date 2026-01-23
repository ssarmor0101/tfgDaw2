<?php

namespace Database\Seeders;

use App\Models\Puntuacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PuntuacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Puntuacion::factory()->count(100)->create();
    }
}
