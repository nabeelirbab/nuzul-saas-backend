<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'users']);
        Permission::create(['name' => 'users.viewAny']);
        Permission::create(['name' => 'users.view']);
        Permission::create(['name' => 'users.create']);
        Permission::create(['name' => 'users.edit']);

        Permission::create(['name' => 'countries']);
        Permission::create(['name' => 'countries.viewAny']);
        Permission::create(['name' => 'countries.view']);
        Permission::create(['name' => 'countries.create']);
        Permission::create(['name' => 'countries.edit']);

        Permission::create(['name' => 'cities']);
        Permission::create(['name' => 'cities.viewAny']);
        Permission::create(['name' => 'cities.view']);
        Permission::create(['name' => 'cities.create']);
        Permission::create(['name' => 'cities.edit']);

        $roleAdmin = Role::create(['name' => 'admin']);
        $roleAdmin->givePermissionTo(Permission::all());
    }
}
