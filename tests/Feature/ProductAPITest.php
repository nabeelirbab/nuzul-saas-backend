<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ProductAPITest extends TestCase
{
    /**
     * As a User, I should be able to see list of the products in the system, so that I it could select it.
     */
    public function testUserCanViewPublishedProductsOnly()
    {
        // adding 3 products
        Product::factory()->create(['status' => 'published']);
        Product::factory()->create(['status' => 'published']);
        Product::factory()->create(['status' => 'draft']);
        $response = $this->getJson('/api/products');

        $response->assertSuccessful();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'name_ar',
                        'name_en',
                        'price',
                        'price_monthly_recurring',
                        'price_quarterly_recurring',
                        'price_yearly_recurring',
                        'tax_percentage',
                        'status',
                        'is_private',
                    ],
                ],
            ]
        );
    }

    /**
     * As an Admin, I should be able to see list of all products in the system, so that I it could control it.
     */
    public function testAdminCanViewAllProducts()
    {
        // adding 3 products
        Product::factory()->create(['status' => 'published']);
        Product::factory()->create(['status' => 'published']);
        Product::factory()->create(['status' => 'draft']);

        // login as a admin
        $this->adminLogin();
        $response = $this->getJson('/api/products');
        $response->assertSuccessful();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'name_ar',
                        'name_en',
                        'price',
                        'price_monthly_recurring',
                        'price_quarterly_recurring',
                        'price_yearly_recurring',
                        'tax_percentage',
                        'status',
                        'is_private',
                    ],
                ],
            ]
        );
    }
}
