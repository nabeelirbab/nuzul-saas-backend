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
        Role::create(['id' => 1, 'name_en' => 'admin', 'name_ar' => 'مدير النظام']);
        Role::create(['id' => 2, 'name_en' => 'company', 'name_ar' => 'شركة']);
        Role::create(['id' => 3, 'name_en' => 'company_owner', 'name_ar' => 'المالك']);
    }
}
