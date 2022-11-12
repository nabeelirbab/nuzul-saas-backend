<?php

namespace Tests\Feature;

use App\Models\District;
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
        $d = District::factory()->create();
        $user = $this->companyAccountLogin();

        $tenant = $user->tenants()->first();

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $data = [
            'name' => 'Abdulmajeed Abdulaziz',
            'email' => 'dd@google.com',
            'mobile_number' => '966501175111',
            'gender' => 'male',
            'district_id' => $d->id,
            'is_property_buyer' => true,
            'is_property_owner' => false,
        ];

        $response = $this->postJson(
            '/api/contacts',
            $data
        );

        $response->assertSuccessful();
    }
}
