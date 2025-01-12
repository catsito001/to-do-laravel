<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tarea;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TareasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->clearImages(); // Elimina las imágenes existentes
        Tarea::truncate();    // Limpia la tabla de tareas

        $faker = Faker::create();

        for ($i = 0; $i < 30; $i++) {
            // Creamos una tarea
            $titulo = $faker->sentence;
            $descripcion = $faker->paragraph;

            // Descargar una imagen fake
            $imageUrl = 'https://picsum.photos/600/400?random=' . rand(1, 1000); // URL de imagen aleatoria
            $imageContents = file_get_contents($imageUrl); // Obtener la imagen
            $imageName = Str::random(10) . '.jpg'; // Nombre único para la imagen

            // Guardamos la imagen en el directorio público de Laravel
            Storage::disk('public')->put('tareas/' . $imageName, $imageContents);

            // Crear la tarea en la base de datos
            Tarea::create([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'imagen' => 'tareas/' . $imageName, // Guardamos el path relativo
            ]);
        }
    }

    /**
     * Elimina todas las imágenes existentes en el directorio `tareas`.
     */
    protected function clearImages(): void
    {
        $directory = 'tareas'; // Directorio donde se almacenan las imágenes
        if (Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->deleteDirectory($directory); // Elimina el directorio y su contenido
        }
        Storage::disk('public')->makeDirectory($directory); // Vuelve a crear el directorio vacío
    }
}
