<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'tenant_id' => 1, // Update with the appropriate tenant ID
            'tenant_contact_id' => null,
            'category' => $this->faker->randomElement(['commercial', 'residential']),
            'purpose' => $this->faker->randomElement(['rent', 'sell']),
            'availability_status' => 'unavailable',
            'availability_date' => null,
            'type' => $this->faker->randomElement([
                'villa',
                'building_apartment',
                'villa_apartment',
                'land',
                'duplex',
                'townhouse',
                'mansion',
                'villa_floor',
                'farm',
                'istraha',
                'store',
                'office',
                'storage',
                'building',
            ]),
            'year_built' => $this->faker->year,
            'street_width' => $this->faker->numberBetween(5, 20),
            'selling_price' => $this->faker->randomFloat(2, 1000, 100000),
            'rent_price_monthly' => $this->faker->randomFloat(2, 500, 5000),
            'rent_price_quarterly' => $this->faker->randomFloat(2, 1000, 10000),
            'rent_price_semi_annually' => $this->faker->randomFloat(2, 2000, 20000),
            'rent_price_annually' => $this->faker->randomFloat(2, 3000, 30000),
            'district_id' => null,
            'area' => $this->faker->randomFloat(2, 50, 1000),
            'unit_number' => $this->faker->buildingNumber,
            'longitude' => $this->faker->longitude,
            'latitude' => $this->faker->latitude,
            'number_of_floors' => $this->faker->numberBetween(1, 10),
            'unit_floor_number' => $this->faker->numberBetween(1, 10),
            'bedrooms' => $this->faker->numberBetween(1, 5),
            'bathrooms' => $this->faker->numberBetween(1, 5),
            'dining_rooms' => $this->faker->numberBetween(0, 3),
            'living_rooms' => $this->faker->numberBetween(0, 3),
            'majlis_rooms' => $this->faker->numberBetween(0, 3),
            'maid_rooms' => $this->faker->numberBetween(0, 2),
            'driver_rooms' => $this->faker->numberBetween(0, 2),
            'mulhaq_rooms' => $this->faker->numberBetween(0, 2),
            'storage_rooms' => $this->faker->numberBetween(0, 2),
            'basement_rooms' => $this->faker->numberBetween(0, 2),
            'elevators' => $this->faker->numberBetween(0, 2),
            'pools' => $this->faker->numberBetween(0, 1),
            'balconies' => $this->faker->numberBetween(0, 3),
            'kitchens' => $this->faker->numberBetween(0, 2),
            'gardens' => $this->faker->numberBetween(0, 1),
            'parking_spots' => $this->faker->numberBetween(0, 5),
            'facade' => $this->faker->randomElement(['north', 'east', 'south', 'west', 'north_east', 'north_west', 'south_east', 'south_west']),
            'is_kitchen_installed' => $this->faker->boolean,
            'is_ac_installed' => $this->faker->boolean,
            'is_parking_shade' => $this->faker->boolean,
            'is_furnished' => $this->faker->boolean,
            'length' => $this->faker->boolean,
            'width' => $this->faker->boolean,
            'cover_image_url' => null,
            'published_on_website' => false,
            'published_on_app' => false,
        ];
    }
}


