<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Package::factory()->create(
            [
                'name_en' => 'Pro',
                'name_ar' => 'الخبير',
                'price_quarterly' => 99,
                'price_yearly' => 1100,
                'tax' => 15.0,
                'status' => 'published',
            ]
        );

        Package::factory()->create(
            [
                'name_en' => 'Team',
                'name_ar' => 'الفريق',
                'price_quarterly' => 290,
                'price_yearly' => 3500,
                'tax' => 15.0,
                'status' => 'published',
            ]
        );

        Package::factory()->create(
            [
                'name_en' => 'Office',
                'name_ar' => 'المكتب',
                'price_quarterly' => 390,
                'price_yearly' => 4700,
                'tax' => 15.0,
                'status' => 'published',
            ]
        );

        Package::factory()->create(
            [
                'name_en' => 'Pro - Trial',
                'name_ar' => 'الخبير - تجربة',
                'price_quarterly' => 0,
                'price_yearly' => 0,
                'tax' => 0,
                'status' => 'published',
                'is_trial' => true,
            ]
        );

        Package::factory()->create(
            [
                'name_en' => 'Team - Trial',
                'name_ar' => 'الفريق - تجربة',
                'price_quarterly' => 0,
                'price_yearly' => 0,
                'tax' => 0,
                'status' => 'published',
                'is_trial' => true,
            ]
        );

        Package::factory()->create(
            [
                'name_en' => 'Office - Trial',
                'name_ar' => 'المكتب - تجربة',
                'price_quarterly' => 0,
                'price_yearly' => 0,
                'tax' => 0,
                'status' => 'published',
                'is_trial' => true,
            ]
        );
    }
}
