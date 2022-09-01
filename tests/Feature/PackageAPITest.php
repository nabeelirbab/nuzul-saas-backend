<?php

namespace Tests\Feature;

use App\Models\Package;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class PackageAPITest extends TestCase
{
    /**
     * As a User, I should be able to see list of the packages in the system, so that I it could select it.
     */
    public function testUserCanViewPublishedPackagesOnly()
    {
        // adding 3 packages
        Package::factory()->create(['status' => 'published']);
        Package::factory()->create(['status' => 'published']);
        Package::factory()->create(['status' => 'draft']);
        $response = $this->getJson('/api/packages');

        $response->assertSuccessful();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'name_ar',
                        'name_en',
                        'price_yearly',
                        'price_monthly',
                        'tax',
                        'status',
                    ],
                ],
            ]
        );
    }

    /**
     * As an Admin, I should be able to see list of all packages in the system, so that I it could control it.
     */
    public function testAdminCanViewAllPackages()
    {
        // adding 3 packages
        Package::factory()->create(['status' => 'published']);
        Package::factory()->create(['status' => 'published']);
        Package::factory()->create(['status' => 'draft']);

        // login as a admin
        $this->adminLogin();
        $response = $this->getJson('/api/packages');
        $response->assertSuccessful();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'name_ar',
                        'name_en',
                        'price_yearly',
                        'price_monthly',
                        'tax',
                        'status',
                    ],
                ],
            ]
        );
    }
}
