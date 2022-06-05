<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class=RoleAndPermissionSeeder');
    }

    public function adminLogin()
    {
        $user = User::factory()->create();
        $user->assignRole(['admin']);

        return Sanctum::actingAs($user);
    }
}
