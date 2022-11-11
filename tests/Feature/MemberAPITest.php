<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class MemberAPITest extends TestCase
{
    /**
     * As a Company Owner, I should be able to view all members, so that I can know who has access.
     */
    public function testCompanyOwnerCanViewAllMembers()
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

        TenantUser::create([
            'user_id' => $user2->id,
            'tenant_id' => $tenant->id,
            'company_role_id' => 5,
        ]);

        Sanctum::actingAs($user);

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $response = $this->getJson(
            '/api/members'
        );

        $response->assertSuccessful();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'mobile_number',
                        'name',
                        'role' => [
                            'role_id',
                            'name_ar',
                            'name_en',
                        ],
                        'created_at',
                    ],
                ],
            ]
        );
    }

    public function testCompanyOwnerRemoveMembers()
    {
        $firstTenant = $this->companyAccountLogin();
        $tenant = $firstTenant->tenants()->first();

        $secondTenant = $this->companyOwnerLogin();

        // first tenant invites second tenant
        Invitation::create(
            [
                'mobile_number' => $secondTenant->mobile_number,
                'tenant_id' => $tenant->id,
                'company_role_id' => 5,
                'expires_at' => now()->addDays(2),
            ]
        );

        // second tenant accepts invitation to be a member in part of first tenant
        TenantUser::create([
            'user_id' => $secondTenant->id,
            'tenant_id' => $tenant->id,
            'company_role_id' => 5,
        ]);

        Sanctum::actingAs($firstTenant);

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        // first tenant gets the first member
        $t = TenantUser::latest()->first();

        // first tenant removes the first member
        $response = $this->deleteJson(
            '/api/members/'.$t->id
        );

        $response->assertSuccessful();
    }

    public function testCompanyOwnerCanNotRemoveMembersFromAnotherTenant()
    {
        $firstTenant = $this->companyAccountLogin();
        $firstTenant = $firstTenant->tenants()->first();

        // creating Mo tenant
        $moData = [
            'name' => 'Mo',
            'password' => bcrypt('secret'),
            'mobile_number' => '96650112211',
            'email' => 'mo@nuzul.app',
            'role_id' => Role::COMPANY,
        ];

        $mo = User::create($moData);

        $secTenant = Tenant::create([
            'id' => 2,
            'name_en' => 'Nuzul mo',
            'name_ar' => 'نزل مو',
        ]);

        $secTenant->users()->attach($mo->id, ['company_role_id' => Role::COMPANY_OWNER]);

        $centralDomains = explode(',', env('CENTRAL_DOMAINS'));
        $secTenant->domains()->create(['domain' => readable_random_string().$secTenant->id.'.'.$centralDomains[1]]);

        // creating Aj tenant
        $ajData = [
            'name' => 'Aj',
            'password' => bcrypt('secret'),
            'mobile_number' => '966502775211',
            'email' => 'aj@nuzul.app',
            'role_id' => Role::COMPANY,
        ];

        $aj = User::create($ajData);

        $thirdTenant = Tenant::create([
            'id' => 3,
            'name_en' => 'Nuzul aj',
            'name_ar' => 'نزل اي جي',
        ]);

        $thirdTenant->users()->attach($aj->id, ['company_role_id' => Role::COMPANY_OWNER]);

        $centralDomains = explode(',', env('CENTRAL_DOMAINS'));
        $thirdTenant->domains()->create(['domain' => readable_random_string().$thirdTenant->id.'.'.$centralDomains[1]]);

        // creating Sa tenant
        $saData = [
            'name' => 'Sa',
            'password' => bcrypt('secret'),
            'mobile_number' => '966501343311',
            'email' => 'sa@nuzul.app',
            'role_id' => Role::COMPANY,
        ];

        $sa = User::create($saData);

        $FourTenant = Tenant::create([
            'id' => 4,
            'name_en' => 'Nuzul sa',
            'name_ar' => 'نزل اس اي',
        ]);

        $FourTenant->users()->attach($sa->id, ['company_role_id' => Role::COMPANY_OWNER]);

        $centralDomains = explode(',', env('CENTRAL_DOMAINS'));
        $FourTenant->domains()->create(['domain' => readable_random_string().$FourTenant->id.'.'.$centralDomains[1]]);

        // Four tenants are ready.

        // Let's add tenant 2 as agent in tenant 3 and login as tenant 4 and try to remove it
        TenantUser::create([
            'user_id' => $mo->id,
            'tenant_id' => $thirdTenant->id,
            'company_role_id' => Role::COMPANY_AGENT,
        ]);

        // Let's add tenant 4 as agent in tenant 3 and login as tenant 4
        TenantUser::create([
            'user_id' => $sa->id,
            'tenant_id' => $thirdTenant->id,
            'company_role_id' => Role::COMPANY_AGENT,
        ]);

        // Login as tenant 4
        Sanctum::actingAs($sa);
        tenancy()->initialize($FourTenant);
        URL::forceRootUrl('http://'.$FourTenant->domains[0]['domain']);

        // First tenant gets the first member
        $t = TenantUser::where([['user_id', $mo->id], ['tenant_id', $thirdTenant->id]])->first();

        // First tenant removes the first member
        $response = $this->deleteJson(
            '/api/members/'.$t->id
        );

        $response->assertUnauthorized();
    }
}
