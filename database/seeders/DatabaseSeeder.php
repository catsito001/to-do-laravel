<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\TareasTableSeeder; // Asegúrate de importar la clase correctamente

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Llama al seeder de Tareas
        $this->call(TareasTableSeeder::class);
    }
}
