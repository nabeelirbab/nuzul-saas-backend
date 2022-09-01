<?php

namespace Database\Seeders;

use App\Models\Region;
use File;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $json = File::get('database/seeders/json/regions.json');
        $regions = json_decode($json);

        foreach ($regions as $key => $value) {
            Region::create([
                'name_ar' => $value->name_ar,
                'name_en' => $value->name_en,
            ]);
        }
    }
}
