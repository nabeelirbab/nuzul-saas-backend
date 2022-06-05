<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        City::factory()->create([
            'name_ar' => 'الرياض',
            'name_en' => 'Riyadh',
            'country_id' => 1,
        ]);
    }
}
