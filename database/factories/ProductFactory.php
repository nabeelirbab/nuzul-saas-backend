<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => 'recurring',
            'name_ar' => 'Gold-trial',
            'name_en' => 'الذهبية-تجربة',
            'price' => 0,
            'price_monthly_recurring' => 0,
            'price_quarterly_recurring' => 0,
            'price_yearly_recurring' => 0,
            'tax_percentage' => 0,
            'status' => 'published',
            'is_private' => true,
        ];
    }
}
