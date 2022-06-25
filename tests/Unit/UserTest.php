<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class UserTest extends TestCase
{
    public function testUserCanBeAdmin()
    {
        $user = User::factory()->create(['role_id' => Role::ADMIN]);
        static::assertTrue(Role::ADMIN === $user->role_id);
    }
}
