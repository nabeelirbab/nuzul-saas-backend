<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class UserAPITest extends TestCase
{
    /**
     * As a user, I should be able to update my profile information, so that I it could be up to date.
     */
    public function testUserCanUpdateProfile()
    {
        // login as a admin
        $this->companyOwnerLogin();
        $response = $this->postJson('/api/me', ['email' => 'm@t.com', 'name' => 'خالد', 'gender' => 'male']);
        $response->assertSuccessful();

        $response->assertJsonStructure(
            [
                'data' => [
                    'name',
                    'gender',
                ],
            ]
        );
    }

    /**
     * As a user, I should be able to update my profile information, so that I it could be up to date.
     */
    public function testUserCanViewOwnProfile()
    {
        $this->companyAccountLogin();
        $response = $this->getJson('/api/me');
        $response->assertSuccessful();

        $response->assertJsonStructure(
            [
                'data' => [
                    'name',
                    'email',
                    'gender',
                    'mobile_number',
                    'role' => [
                        'role_id',
                        'name_ar',
                        'name_en',
                    ],
                    'workspaces' => [
                        '*' => [
                            'id',
                            'name_en',
                            'name_ar',
                            'company_role' => [
                                'role_id',
                                'name_ar',
                                'name_en',
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
}
