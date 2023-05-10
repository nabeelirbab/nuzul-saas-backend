<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Property;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class PropertyAPITest extends TestCase
{
    /**
     * As a public user, I should be able to see tenant's properties, so that I can know what they have.
     */
    public function testPublicUserCanSeeTenantProperties()
    {
        // we should have a tenant with properties
        // we should hit the endpoint to get the properties
        // we should see the properties
        // create
        $c = City::factory()->create();
        $user = $this->companyAccountLogin();

        $tenant = $user->tenants()->first();

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $propertyData = [
            'tenant_id' => $tenant->id,
            'category' => 'commercial',
            'purpose' => 'rent',
            'availability_status' => 'available',
            'type' => 'villa',
            'unit_number' => '123',
            'published_on_website' => true,
        ];

        Property::factory()->create($propertyData);

        $response = $this->getJson(
            '/api/public/properties',
        );

        $response->assertSuccessful();
    }
}
