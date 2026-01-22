<?php

namespace Database\Seeders;

use App\Enums\RolSlug;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function Symfony\Component\Clock\now;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRol = Rol::where('slug', RolSlug::ADMIN)->first()->id;
        User::firstOrCreate([
            'name' => 'Admin',
            'email' => 'admin@admin.es',
            'email_verified_at' => now(),
            'password' => bcrypt('1234'),
            'rol_id' => $adminRol
        ]);

        $userRol = Rol::where('slug', RolSlug::USER)->first()->id;
        User::firstOrCreate([
            'name' => 'Test User',
            'email' => 'test@test.es',
            'password' => bcrypt('1234'),
            'rol_id' => $userRol
        ]);
    }
}
