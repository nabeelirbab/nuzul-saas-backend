<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $user = User::factory()->create([
            'name' => 'Bin Nasser',
            'email' => 'abdulmajeednasser@gmail.com',
            'mobile_number' => '966501111111',
            'password' => bcrypt('P@ssw0rd123'),
            'role_id' => Role::ADMIN,
        ]);
    }
}
