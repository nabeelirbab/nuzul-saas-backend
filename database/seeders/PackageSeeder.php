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
                'name_ar' => 'Pro',
                'name_en' => 'الخبير',
                'price_quarterly' => 99,
                'price_yearly' => 1100,
                'tax' => 15.0,
                'status' => 'published',
            ]
        );

        Package::factory()->create(
            [
                'name_ar' => 'Team',
                'name_en' => 'الفريق',
                'price_quarterly' => 290,
                'price_yearly' => 3500,
                'tax' => 15.0,
                'status' => 'published',
            ]
        );

        Package::factory()->create(
            [
                'name_ar' => 'Office',
                'name_en' => 'المكتب',
                'price_quarterly' => 390,
                'price_yearly' => 4700,
                'tax' => 15.0,
                'status' => 'published',
            ]
        );

        Package::factory()->create(
            [
                'name_ar' => 'Pro-Trial',
                'name_en' => 'الخبير-تجربة',
                'price_quarterly' => 0,
                'price_yearly' => 0,
                'tax' => 0,
                'status' => 'published',
                'is_trial' => true,
            ]
        );

        Package::factory()->create(
            [
                'name_ar' => 'Team-Trial',
                'name_en' => 'الفريق-تجربة',
                'price_quarterly' => 0,
                'price_yearly' => 0,
                'tax' => 0,
                'status' => 'published',
                'is_trial' => true,
            ]
        );

        Package::factory()->create(
            [
                'name_ar' => 'Office-Trial',
                'name_en' => 'المكتب-تجربة',
                'price_quarterly' => 0,
                'price_yearly' => 0,
                'tax' => 0,
                'status' => 'published',
                'is_trial' => true,
            ]
        );
    }
}
