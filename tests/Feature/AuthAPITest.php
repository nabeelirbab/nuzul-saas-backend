<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Role;
use App\Models\Tenant;
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
        $userData = [
            'name' => 'Raz',
            'password' => bcrypt('secret'),
            'mobile_number' => '966501175111',
            'email' => 'user@nuzul.app',
            'role_id' => Role::COMPANY,
        ];

        $user = User::create($userData);

        $tenant = Tenant::create([
            'id' => 1,
            'name_en' => 'Nuzul x',
            'name_ar' => 'نزل اكس',
        ]);

        $tenant->users()->attach($user->id, ['company_role_id' => Role::COMPANY_OWNER]);

        $centralDomains = explode(',', env('CENTRAL_DOMAINS'));

        $tenant->domains()->create(['domain' => readable_random_string().$tenant->id.'.'.$centralDomains[1]]);

        $tenant2 = Tenant::create(
            [
                'id' => 2,
                'name_en' => 'Spaces',
                'name_ar' => 'سبيسز',
            ]
        );

        Invitation::create([
            'mobile_number' => '966501175111',
            'tenant_id' => $tenant2->id,
            'expires_at' => now()->addDays(5),
            'company_role_id' => 5,
        ]);

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
                    'pending_invitations' => ['*' => [
                        'id',
                        'mobile_number',
                        'status',
                        'workspace' => [
                            'name_ar',
                            'name_en',
                        ],
                    ],
                    ],
                    'workspaces' => ['*' => [
                        'id',
                        'is_default',
                        'name_en',
                        'name_ar',
                        'active',
                        'company_role',
                        'domain',
                    ],
                    ],
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
        static::assertSame($response->json()['data']['role']['name_en'], 'Company');
    }
}
