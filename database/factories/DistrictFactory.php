<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

class DistrictFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = District::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $district_name = $this->faker->city;

        return [
            'city_id' => City::factory(),
            'name_ar' => $district_name,
            'name_en' => $district_name,
            'boundaries' => '[12,34]',
        ];
    }
}
