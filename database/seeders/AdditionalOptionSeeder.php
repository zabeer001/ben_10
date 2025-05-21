<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdditionalOption;

class AdditionalOptionSeeder extends Seeder
{
    public function run()
    {
        AdditionalOption::insert([
            [
                'name' => 'Sunroof',
                'price' => 1200,
                'vehicle_model_id' => 1,
                'category_name' => 'Comfort',
                'type' => 'Optional',
            ],
            [
                'name' => 'Leather Seats',
                'price' => 1500,
                'vehicle_model_id' => 1,
                'category_name' => 'Interior',
                'type' => 'Optional',
            ],
            [
                'name' => 'Premium Sound System',
                'price' => 800,
                'vehicle_model_id' => 2,
                'category_name' => 'Entertainment',
                'type' => 'Optional',
            ],
            [
                'name' => 'Tow Package',
                'price' => 700,
                'vehicle_model_id' => 3,
                'category_name' => 'Utility',
                'type' => 'Optional',
            ],
        ]);
    }
}
