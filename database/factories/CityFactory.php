<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
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
        $latitude = $this->faker->latitude;
        $longitude = $this->faker->longitude;

        return [
            'country_id' => Country::factory(),
            'region_id' => Region::factory(),
            'name_ar' => $city_name,
            'name_en' => $city_name,
            'longitude' => $longitude,
            'latitude' => $latitude,
        ];
    }
}
