<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\TenantContact;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ContactAPITest extends TestCase
{
    /**
     * As a Tenant, I should be able to create new contact, so that I it could fulfill their needs it.
     */
    public function testTenantCanCreateContact()
    {
        // create
        $c = City::factory()->create();
        $user = $this->companyAccountLogin();

        $tenant = $user->tenants()->first();

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $data = [
            'name' => 'Abdulmajeed Abdulaziz',
            'email' => 'dd@google.com',
            'mobile_number' => '966501175111',
            'gender' => 'male',
            'city_id' => $c->id,
            'is_property_buyer' => true,
            'is_property_owner' => false,
        ];

        $response = $this->postJson(
            '/api/contacts',
            $data
        );

        $response->assertSuccessful();
    }

    /**
     * As a Tenant, I should be able to get total contacts, so that I it could know how many clients I have.
     */
    public function testTenantCanGetTotalContacts()
    {
        // create
        $c = City::factory()->create();
        $user = $this->companyAccountLogin();

        $tenant = $user->tenants()->first();

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $data = [
            'tenant_id' => $tenant->id,
        ];

        TenantContact::factory()->create($data);

        $response = $this->getJson(
            '/api/dashboard/clients/total'
        );

        $response->assertSuccessful();
    }

    public function testTenantCanGetContactsGrowth()
    {
        // create
        $c = City::factory()->create();
        $user = $this->companyAccountLogin();

        $tenant = $user->tenants()->first();

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $data = [
            'tenant_id' => $tenant->id,
        ];

        TenantContact::factory()->create($data);

        $response = $this->getJson(
            '/api/dashboard/clients/growth'
        );

        $response->assertSuccessful();
    }
}
