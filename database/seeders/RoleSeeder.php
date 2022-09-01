<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Role::create(['id' => 1, 'name_en' => 'Admin', 'name_ar' => 'مدير النظام']);
        Role::create(['id' => 2, 'name_en' => 'Company', 'name_ar' => 'شركة']);
        Role::create(['id' => 3, 'name_en' => 'Company Owner', 'name_ar' => 'مالك']);
        Role::create(['id' => 4, 'name_en' => 'Company Manager', 'name_ar' => 'مدير']);
        Role::create(['id' => 5, 'name_en' => 'Agent', 'name_ar' => 'متعاون']);
    }
}
