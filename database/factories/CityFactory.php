<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = City::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $city_name = $this->faker->city;

        return [
            'country_id' => Country::factory(),
            'name_ar' => $city_name,
            'name_en' => $city_name,
        ];
    }
}
