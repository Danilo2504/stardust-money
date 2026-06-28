<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    // use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Hogar', 'color' => '#FF5733'],
            ['name' => 'Alimentación', 'color' => '#33FF57'],
            ['name' => 'Transporte', 'color' => '#5733FF'],
            ['name' => 'Trabajo / Negocio', 'color' => '#FF33A1'],
            ['name' => 'Educación', 'color' => '#33A1FF'],
            ['name' => 'Salud', 'color' => '#A1FF33'],
            ['name' => 'Compras personales', 'color' => '#FF8C33'],
            ['name' => 'Ocio y entretenimiento', 'color' => '#8C33FF'],
            ['name' => 'Viajes', 'color' => '#33FF8C'],
            ['name' => 'Finanzas', 'color' => '#FF3380'],
            ['name' => 'Familia / Social', 'color' => '#3380FF'],
            ['name' => 'Mascotas', 'color' => '#80FF33'],
            ['name' => 'Suscripciones', 'color' => '#FF8033'],
            ['name' => 'Varios', 'color' => '#8033FF'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name'], 'is_default' => true],
                [
                    'is_default' => true,
                    'user_id' => null,
                    'color' => $category['color'],
                ]
            );
        }
    }
}
