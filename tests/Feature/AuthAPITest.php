<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class AuthAPITest extends TestCase
{
    /**
     * As a user, I should be able to login, so that I can access my account.
     */
    public function testUserCanLoginWithCorrectCredentials()
    {
        User::factory()->create(['email' => 'user@salem.com', 'password' => Hash::make('secret')]);

        $response = $this->postJson('/api/login', ['email' => 'user@salem.com', 'password' => 'secret']);
        $response->assertStatus(200);
        $response->assertJsonStructure(['plainTextToken']);
    }

    /**
     * User should NOT be able to login with incorrect credentials.
     */
    public function testUserCanNotLoginWithIncorrectCredentials()
    {
        User::factory()->create(['email' => 'user@salem.com', 'password' => Hash::make('secret')]);

        $response = $this->postJson('/api/login', ['email' => 'user@salem.com', 'password' => 'password']);
        $response->assertJsonValidationErrors('email');
        $response->assertSeeText('The provided credentials are incorrect.');
    }
}
