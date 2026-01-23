<?php

namespace Database\Seeders;

use App\Models\Resultado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResultadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Resultado::factory()->count(5)->create();
    }
}
