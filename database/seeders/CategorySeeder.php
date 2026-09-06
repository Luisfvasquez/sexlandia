<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Categorías de la tienda SEXLANDIA (sex shop).
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Succionadores', 'description' => 'Estimuladores de clítoris por ondas de presión y succión.'],
            ['name' => 'Vibradores', 'description' => 'Vibradores, conejitos y estimuladores de punto G.'],
            ['name' => 'Lubricantes', 'description' => 'Lubricantes base agua, base silicona y potenciadores.'],
            ['name' => 'Juegos y Parejas', 'description' => 'Accesorios, esposas y juguetes con control remoto para dos.'],
            ['name' => 'Lencería', 'description' => 'Lencería, disfraces y prendas atrevidas.'],
            ['name' => 'Bienestar Íntimo', 'description' => 'Feromonas, aromas, higiene íntima y cuidado personal.'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
