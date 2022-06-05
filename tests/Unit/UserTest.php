<?php

namespace Tests\Unit;

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
        $user = User::factory()->create();
        $user->assignRole(['admin']);
        static::assertTrue($user->hasRole('admin'));
    }
}
