<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\District;
use App\Models\Property;
use App\Models\TenantContact;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class DealAPITest extends TestCase
{
    /**
     * As a Tenant, I should be able to create new deal, so that I it could find a matching property.
     */
    public function testTenantCanCreateDeal()
    {
        // create
        $c = City::factory()->create();
        $d = District::factory()->create();
        $user = $this->companyAccountLogin();

        $tenant = $user->tenants()->first();

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $contact = Contact::factory()->create([
            'name' => 'Abdulmajeed Abdulaziz',
            'email' => 'dd@google.com',
            'mobile_number' => '966501175111',
            'gender' => 'male',
        ]);

        $tc = TenantContact::factory()->create([
            'contact_name_by_tenant' => 'Majeed',
            'tenant_id' => $tenant->id,
            'contact_id' => $contact->id,
            'is_property_buyer' => false,
            'is_property_owner' => true,
            'city_id' => $c->id,
        ]);

        $data = [
            'tenant_contact_id' => $tc->id,
            'category' => 'residential',
            'purpose' => 'rent',
            'type' => 'villa',
        ];

        $response = $this->postJson(
            '/api/deals',
            $data
        );

        $response->assertJsonStructure(
            [
                'data' => [
                    'contact' => [
                        'id',
                        'name',
                        'mobile_number',
                    ],
                    'property',
                    'stage',
                    'category',
                    'purpose',
                    'type',
                    'min_price',
                    'max_price',
                    'min_area',
                    'max_area',
                    'bedrooms',
                    'bathrooms',
                    'facade',
                    'is_kitchen_installed',
                    'is_ac_installed',
                    'is_furnished',
                    'districts',
                    'created_at',
                    'updated_at',
                ],
            ]
        );
        $response->assertSuccessful();
    }

    /**
     * As a Tenant, I should be able to update a deal, so that I it could find a matching property.
     */
    public function testTenantCanUpdateDeal()
    {
        // create
        $d = District::factory()->create();
        $user = $this->companyAccountLogin();

        $tenant = $user->tenants()->first();

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $contact = Contact::factory()->create([
            'name' => 'Abdulmajeed Abdulaziz',
            'email' => 'dd@google.com',
            'mobile_number' => '966501175111',
            'gender' => 'male',
        ]);

        $tc = TenantContact::factory()->create([
            'contact_name_by_tenant' => 'Majeed',
            'tenant_id' => $tenant->id,
            'contact_id' => $contact->id,
            'is_property_buyer' => false,
            'is_property_owner' => true,
            'district_id' => $d->id,
        ]);

        $deal = Deal::create(
            [
                'tenant_contact_id' => $tc->id,
                'tenant_id' => $tenant->id,
                'category' => 'residential',
                'purpose' => 'rent',
                'type' => 'villa',
            ]
        );

        $deal->districts()->sync([$d->id]);

        $p = Property::factory()->create(
            [
                'tenant_id' => $tenant->id,
                'category' => 'commercial',
                'listing_purpose' => 'sell',
                'type' => 'villa',
                'district_id' => $d->id,
            ]
        );

        $data = [
            'property_id' => $p->id,
            'stage' => 'visit',
            'rent_period' => 'quarterly',
            'min_price' => 100000,
            'max_price' => 200000,
            'min_area' => 100,
            'max_area' => 200,
            'bedrooms' => 4,
            'bathrooms' => 2,
            'facade' => 'north',
            'is_kitchen_installed' => '0',
            'is_ac_installed' => '0',
            'is_furnished' => '0',
            'districts' => [],
        ];

        $response = $this->putJson(
            '/api/deals/'.$deal->id,
            $data
        );
        $response->assertSuccessful();

        $response->assertJsonStructure(
            [
                'data' => [
                    'contact' => [
                        'id',
                        'name',
                        'mobile_number',
                    ],
                    'property',
                    'stage',
                    'category',
                    'purpose',
                    'type',
                    'min_price',
                    'max_price',
                    'min_area',
                    'max_area',
                    'bedrooms',
                    'bathrooms',
                    'facade',
                    'is_kitchen_installed',
                    'is_ac_installed',
                    'is_furnished',
                    'created_at',
                    'updated_at',
                ],
            ]
        );
    }
}
