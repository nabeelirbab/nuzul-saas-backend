<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Product::factory()->create(
            [
                'type' => 'recurring',
                'name_ar' => 'مقعد - تجريبي',
                'name_en' => 'Seat - trial',
                'price' => 0,
                'price_monthly_recurring' => 0,
                'price_quarterly_recurring' => 0,
                'price_yearly_recurring' => 0,
                'tax_percentage' => 0,
                'status' => 'published',
                'is_private' => false,
            ]
        );
    }
}
