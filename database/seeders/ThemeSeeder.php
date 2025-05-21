<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('themes')->insert([
            [
                'name' => 'Modern Minimalist',
                'image' => 'images/themes/modern_minimalist.png',
                'flooring_name' => 'Light Oak',
                'flooring_image' => 'images/floorings/light_oak.png',
                'cabinetry_1_name' => 'Matte White',
                'cabinetry_1_image' => 'images/cabinetry/matte_white.png',
                'table_top_1_name' => 'Quartz White',
                'table_top_1_image' => 'images/table_tops/quartz_white.png',
                'seating_1_name' => 'Steel Stool',
                'seating_1_image' => 'images/seating/steel_stool.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rustic Charm',
                'image' => 'images/themes/rustic_charm.png',
                'flooring_name' => 'Distressed Wood',
                'flooring_image' => 'images/floorings/distressed_wood.png',
                'cabinetry_1_name' => 'Barnwood',
                'cabinetry_1_image' => 'images/cabinetry/barnwood.png',
                'table_top_1_name' => 'Butcher Block',
                'table_top_1_image' => 'images/table_tops/butcher_block.png',
                'seating_1_name' => 'Leather Chair',
                'seating_1_image' => 'images/seating/leather_chair.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Urban Industrial',
                'image' => 'images/themes/urban_industrial.png',
                'flooring_name' => 'Concrete Grey',
                'flooring_image' => 'images/floorings/concrete_grey.png',
                'cabinetry_1_name' => 'Charcoal Steel',
                'cabinetry_1_image' => 'images/cabinetry/charcoal_steel.png',
                'table_top_1_name' => 'Reclaimed Wood',
                'table_top_1_image' => 'images/table_tops/reclaimed_wood.png',
                'seating_1_name' => 'Metal Barstool',
                'seating_1_image' => 'images/seating/metal_barstool.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
