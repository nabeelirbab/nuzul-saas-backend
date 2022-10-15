<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name_ar' => 'Gold',
            'name_en' => 'الذهبية',
            'price_quarterly' => 100,
            'price_yearly' => 1100,
            'tax' => 15.0,
            'status' => 'draft',
        ];
    }
}
