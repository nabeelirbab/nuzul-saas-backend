<?php

namespace Database\Seeders;

use App\Models\City;
use File;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $json = File::get('database/seeders/json/cities.json');
        $cities = json_decode($json);

        foreach ($cities as $key => $value) {
            City::factory()->create([
                'id' => $value->city_id,
                'name_ar' => $value->name_ar,
                'name_en' => $value->name_en,
                'country_id' => 1,
                'region_id' => $value->region_id,
                'latitude' => $value->center[0],
                'longitude' => $value->center[1],
            ]);
        }
    }
}
