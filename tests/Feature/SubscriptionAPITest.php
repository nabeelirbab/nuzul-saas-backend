<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class SubscriptionAPITest extends TestCase
{
    /**
     * As a Company Owner, I should be able to see my subscriptions, so that I it could know the history of my subscription.
     */
    public function testOwnerCanViewSubscriptions()
    {
        $p = Product::factory()->create(
            [
                'type' => 'recurring',
                'name_en' => 'Seat',
                'name_ar' => 'مقعد',
                'price' => 0,
                'price_monthly_recurring' => 0,
                'price_quarterly_recurring' => 100,
                'price_yearly_recurring' => 0,
                'tax_percentage' => 15,
                'status' => 'published',
                'is_private' => false,
            ]
        );

        $user = $this->companyAccountLogin();
        $tenant = $user->tenants()->first();

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $response = $this->postJson(
            '/api/orders',
            [
                'is_trial' => true,
            ]
        );

        $response->assertSuccessful();
        $response->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'total_amount_without_tax',
                    'total_amount_with_tax',
                    'type',
                    'status',
                    'created_at',
                    'updated_at',
                    'transactions',
                ],
            ]
        );

        $response = $this->getJson(
            '/api/subscriptions'
        );

        $response->assertSuccessful();
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'orders' => [
                            '*' => [
                                'id',
                                'total_amount_without_tax',
                                'total_amount_with_tax',
                                'type',
                                'status',
                                'created_at',
                                'updated_at',
                            ], ],
                        'start_date',
                        'end_date',
                        'status',
                        'is_trial',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]
        );
    }
}
