<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CityAPITest extends TestCase
{
    /**
     * As an Admin, I should be able to create a city, so that I it could be used in the system.
     */
    public function testAdminCanCreateACity()
    {
        // login as a admin
        $this->adminLogin();
        $country = Country::factory()->create(['name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia', 'active' => '1']);
        $region = Region::factory()->create();
        $response = $this->postJson('/api/cities', ['country_id' => $country->id, 'region_id' => $region->id, 'name_ar' => 'الرياض', 'name_en' => 'Riyadh', 'latitude' => '33,444', 'longitude' => '23,333']);
        $response->assertSuccessful();
        $response->assertJsonStructure(
            [
                'data' => [
                    'country_id',
                    'name_ar',
                    'name_en',
                    'region_id',
                    'latitude',
                    'longitude',
                ],
            ]
        );
    }

    /**
     * As a User, I should NOT be able to create a city, so that I it could only be created by admin of the system.
     */
    public function testNotAdminCanNotCreateACity()
    {
        $response = $this->postJson('/api/cities', ['name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia']);
        $response->assertUnauthorized();
    }

    /**
     * As a User, I should be able to see list of the cities in the system, so that I it could select it.
     */
    public function testUserCanViewActiveCitiesOnly()
    {
        // adding 3 cities
        $city = City::factory()->create(['active' => '1']);
        City::factory()->create(['active' => '1']);
        City::factory()->create(['active' => '0']);
        // dd($city);
        $this->withoutExceptionHandling();
        $response = $this->getJson('/api/cities');

        $response->assertSuccessful();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'country_id',
                        'name_ar',
                        'name_en',
                    ],
                ],
            ]
        );
    }

    /**
     * As an Admin, I should be able to see list of all cities in the system, so that I it could control it.
     */
    public function testAdminCanViewAllCities()
    {
        // adding 3 cities
        City::factory()->create(['active' => '1']);
        City::factory()->create(['active' => '1']);
        City::factory()->create(['active' => '0']);

        // login as a admin
        $this->adminLogin();
        $response = $this->getJson('/api/cities');
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
     * As an Admin, I should be able to update a city, so that I it could be used in the system.
     */
    public function testAdminCanUpdateACity()
    {
        $this->withoutExceptionHandling();
        $city = City::factory()->create(['name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia', 'active' => '1']);
        $country = City::factory()->create();
        // login as a admin
        $region = Region::factory()->create();

        $this->adminLogin();
        $response = $this->putJson('/api/cities/'.$city->id, ['country_id' => $country->id, 'region_id' => $region->id, 'name_ar' => 'باكستان', 'name_en' => 'Pakistan', 'latitude' => '33,444', 'longitude' => '23,333']);
        $response->assertSuccessful();
        $response->assertJsonStructure(
            [
                'data' => [
                    'name_ar',
                    'name_en',
                    'country_id',
                ],
            ]
        );
        static::assertSame($response->json()['data']['name_ar'], 'باكستان');
        static::assertSame($response->json()['data']['name_en'], 'Pakistan');
        static::assertSame($response->json()['data']['country_id'], $country->id);
    }

    /**
     * As a User, I should NOT be able to update a city, so that I it could only be updated by admin of the system.
     */
    public function testNotAdminCanNotUpdateACity()
    {
        $city = City::factory()->create(['name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia', 'active' => '1']);

        $response = $this->putJson('/api/cities/'.$city->id, ['name_ar' => 'باكستان', 'name_en' => 'Pakistan', 'active' => '0']);
        $response->assertUnauthorized();
    }
}
