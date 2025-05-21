<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            [
                'name' => 'Red',
                'code' => '#FF0000',
                'image' => 'images/colors/red.png',
                'status' => 1,
            ],
            [
                'name' => 'Blue',
                'code' => '#0000FF',
                'image' => 'images/colors/blue.png',
                'status' => 1,
            ],
            [
                'name' => 'Green',
                'code' => '#00FF00',
                'image' => 'images/colors/green.png',
                'status' => 0,
            ],
            [
                'name' => 'Yellow',
                'code' => '#FFFF00',
                'image' => 'images/colors/yellow.png',
                'status' => 1,
            ],
        ];

        foreach ($colors as $color) {
            Color::create($color);
        }
    }
}
