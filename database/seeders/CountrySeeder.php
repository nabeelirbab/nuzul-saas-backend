<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Country::factory()->create([
            'name_ar' => 'السعودية',
            'name_en' => 'Saudi Arabia',
        ]);
    }
}
