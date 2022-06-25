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
        User::factory()->create(['mobile_number' => '966501175111', 'email' => 'user@salem.com', 'password' => Hash::make('secret')]);

        $response = $this->postJson('/api/login', ['mobile_number' => '966501175111', 'password' => 'secret']);
        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'data' => [
                    'token',
                    'email',
                    'mobile_number',
                    'role',
                    'name',
                ],
            ]
        );
    }

    /**
     * User should NOT be able to login with incorrect credentials.
     */
    public function testUserCanNotLoginWithIncorrectCredentials()
    {
        User::factory()->create(['mobile_number' => '966501175111', 'password' => Hash::make('secret')]);

        $response = $this->postJson('/api/login', ['mobile_number' => '966501175111', 'password' => 'password']);
        $response->assertJsonValidationErrors('mobile_number');
        $response->assertSeeText('The provided credentials are incorrect.');
    }

    /**
     * As a user, I should be able to register after verifying my mobile number, so that I can access my account.
     */
    public function testUserCanRegisterAfterVerification()
    {
        // user request OTP
        $response = $this->post('/api/send-sms', ['mobile_number' => '966501175111']);
        $response->assertSuccessful();

        // user verify OTP
        $response = $this->postJson('/api/verify-code', ['mobile_number' => '966501175111', 'code' => '1111']);

        $response->assertSuccessful();
        $response->assertJsonStructure(['token']);

        // user gets token
        $token = $response->json()['token'];
        $userData = ['name' => 'Abdulmajeed', 'password' => 'Hello@nuzul234', 'token' => $token, 'email' => 'abdulmajeed@nuzul.com'];

        $response = $this->postJson('/api/register', $userData);
        $response->assertSuccessful();
        static::assertSame($response->json()['data']['role'], 'company');
    }
}
