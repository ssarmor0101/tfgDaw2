<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rol::firstOrCreate(['slug' => 'admin', 'nombre' => 'Administrador']);
        Rol::firstOrCreate(['slug' => 'user', 'nombre' => 'Usuario']);
    }
}
