<?php

namespace Tests\Feature;

use App\Models\Invitation;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class InvitationAPITest extends TestCase
{
    /**
     * As a Company Owner, I should be able to view all invitations, so that I can know it's status.
     */
    public function testCompanyOwnerCanViewAllInvitations()
    {
        $user = $this->companyAccountLogin();
        $tenant = $user->tenants()->first();

        $user2 = $this->companyOwnerLogin();

        Invitation::create(
            [
                'mobile_number' => $user2->mobile_number,
                'tenant_id' => $tenant->id,
                'company_role_id' => 5,
                'expires_at' => now()->addDays(2),
            ]
        );

        Invitation::create(
            [
                'mobile_number' => '966505543999',
                'tenant_id' => $tenant->id,
                'company_role_id' => 5,
                'expires_at' => now()->addDays(2),
            ]
        );

        $response = $this->getJson(
            '/api/invitations'
        );

        $response->assertSuccessful();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'mobile_number',
                        'status',
                        'workspace' => [
                            'name_ar',
                            'name_en',
                        ],
                        'role' => [
                            'name_ar',
                            'name_en',
                        ],
                        'expires_at',
                        'created_at',
                    ],
                ],
            ]
        );
    }

    public function testCompanyOwnerCanSendAnInvitation()
    {
        $user = $this->companyAccountLogin();
        $tenant = $user->tenants()->first();

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $data = [
            'mobile_number' => '966501175311',
            'role_id' => 5,
        ];

        $response = $this->postJson(
            '/api/invitations',
            $data
        );
        $response->assertSuccessful();
    }

    public function testCompanyOwnerCanNotSendAnInvitationForExistingMember()
    {
        $user = $this->companyAccountLogin();
        $tenant = $user->tenants()->first();

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $data = [
            'mobile_number' => '966501175111',
            'role_id' => 5,
        ];

        $response = $this->postJson(
            '/api/invitations',
            $data
        );
        $response->assertUnprocessable();
    }

    public function testCompanyOwnerCanCancelAnInvitation()
    {
        $user = $this->companyAccountLogin();
        $tenant = $user->tenants()->first();

        $invite = Invitation::create(
            [
                'mobile_number' => '966501144333',
                'tenant_id' => $tenant->id,
                'company_role_id' => 5,
                'expires_at' => now()->addDays(2),
            ]
        );

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $response = $this->putJson(
            "/api/invitations/{$invite->id}/cancel",
        );
        $response->assertSuccessful();
    }

    public function testUserCanDeclineAnInvitation()
    {
        $user = $this->companyAccountLogin();
        $tenant = $user->tenants()->first();

        $user = $this->companyOwnerLogin();

        $invite = Invitation::create(
            [
                'mobile_number' => $user->mobile_number,
                'tenant_id' => $tenant->id,
                'company_role_id' => 5,
                'expires_at' => now()->addDays(2),
            ]
        );

        $response = $this->putJson(
            "/api/invitations/{$invite->id}/decline",
        );
        $response->assertSuccessful();
    }
}
