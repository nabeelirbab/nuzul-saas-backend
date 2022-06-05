<?php

namespace Tests\Feature;

use App\Models\Country;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CountryAPITest extends TestCase
{
    /**
     * As an Admin, I should be able to create a country, so that I it could be used in the system.
     */
    public function testAdminCanCreateACountry()
    {
        //login as a admin
        $this->adminLogin();
        $response = $this->postJson('/api/countries', ['name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia']);
        $response->assertSuccessful();
        $response->assertJsonStructure(
            [
                'data' => [
                    'name_ar',
                    'name_en',
                ],
            ]
        );
    }

    /**
     * As a User, I should NOT be able to create a country, so that I it could only be created by admin of the system.
     */
    public function testNotAdminCanNotCreateACountry()
    {
        $response = $this->postJson('/api/countries', ['name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia']);
        $response->assertUnauthorized();
    }

    /**
     * As a User, I should be able to see list of the countries in the system, so that I it could select it.
     */
    public function testUserCanViewActiveCountriesOnly()
    {
        //adding 3 counties
        Country::factory()->create(['active' => '1']);
        Country::factory()->create(['active' => '1']);
        Country::factory()->create(['active' => '0']);

        $this->withoutExceptionHandling();
        $response = $this->getJson('/api/countries');
        $response->assertSuccessful();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'name_ar',
                        'name_en',
                    ],
                ],
            ]
        );
    }

    /**
     * As an Admin, I should be able to see list of all countries in the system, so that I it could control it.
     */
    public function testAdminCanViewAllCountries()
    {
        //adding 3 counties
        Country::factory()->create(['active' => '1']);
        Country::factory()->create(['active' => '1']);
        Country::factory()->create(['active' => '0']);

        //login as a admin
        $this->adminLogin();
        $response = $this->getJson('/api/countries');
        $response->assertSuccessful();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'name_ar',
                        'name_en',
                    ],
                ],
            ]
        );
    }

    /**
     * As an Admin, I should be able to update a country, so that I it could be used in the system.
     */
    public function testAdminCanUpdateACountry()
    {
        $country = Country::factory()->create(['name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia', 'active' => true]);

        //login as a admin
        $this->adminLogin();
        $response = $this->putJson('/api/countries/'.$country->id, ['name_ar' => 'باكستان', 'name_en' => 'Pakistan', 'active' => false]);
        $response->assertSuccessful();
        $response->assertJsonStructure(
            [
                'data' => [
                    'name_ar',
                    'name_en',
                ],
            ]
        );
        static::assertSame($response->json()['data']['name_ar'], 'باكستان');
        static::assertSame($response->json()['data']['name_en'], 'Pakistan');
    }

    /**
     * As a User, I should NOT be able to update a country, so that I it could only be updated by admin of the system.
     */
    public function testNotAdminCanNotUpdateACountry()
    {
        $country = Country::factory()->create(['name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia', 'active' => true]);

        $response = $this->putJson('/api/countries/'.$country->id, ['name_ar' => 'باكستان', 'name_en' => 'Pakistan', 'active' => false]);
        $response->assertUnauthorized();
    }
}
