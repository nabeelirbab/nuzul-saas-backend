<?php

namespace Database\Seeders;

use App\Models\District;
use File;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $json = File::get('database/seeders/json/districts.json');
        $districts = json_decode($json);

        foreach ($districts as $key => $value) {
            District::factory()->create([
                'id' => $value->district_id,
                'name_ar' => $value->name_ar,
                'name_en' => $value->name_en,
                'city_id' => $value->city_id,
                'boundaries' => json_encode($value->boundaries),
            ]);
        }
    }
}
