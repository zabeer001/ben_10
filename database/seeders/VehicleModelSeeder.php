<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleModel;

class VehicleModelSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'name' => 'SRC-14',
                'sleep_person' => '2-3',
                'description' => 'Welcome to the world of comfort and mobility.',
                'category_id' => 1,
                'base_price' => '79500.00',
                'price' => '79500.00',
            ],
            [
                'name' => 'SRC-18',
                'sleep_person' => '3-4',
                'description' => 'Ideal for couples or small families.',
                'category_id' => 2,
                'base_price' => '85900.00',
                'price' => '85900.00',
            ],
            [
                'name' => 'SRC-21',
                'sleep_person' => '4-5',
                'description' => 'Spacious layout with luxury fittings.',
                'category_id' => 1,
                'base_price' => '89900.00',
                'price' => '89900.00',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            VehicleModel::create($vehicle);
        }
    }
}
