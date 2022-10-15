<?php

namespace Tests\Feature;

use App\Models\Package;
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
        $package = Package::factory()->create(
            [
                'name_ar' => 'Gold-trial',
                'name_en' => 'الذهبية-تجربة',
                'price_quarterly' => 0,
                'price_yearly' => 0,
                'tax' => 0,
                'status' => 'published',
                'is_trial' => true,
            ]
        );

        $user = $this->companyAccountLogin();
        $tenant = $user->tenants()->first();

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $response = $this->postJson(
            '/api/orders',
            [
                'package_id' => $package->id,
                'period' => 'quarterly',
                'payment_method' => 'bank_transfer',
            ]
        );

        $response->assertSuccessful();
        $response->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'package_price_quarterly',
                    'package_price_yearly',
                    'package_tax',
                    'tax_amount',
                    'total_amount',
                    'period',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ]
        );

        // static::assertSame($response->json()['data']['package_price_quarterly'], 0);
        // static::assertSame($response->json()['data']['package_price_yearly'], 0);
        // static::assertSame($response->json()['data']['package_tax'], 0);
        // static::assertSame($response->json()['data']['tax_amount'], 0);
        // static::assertSame($response->json()['data']['total_amount'], 0);
        // static::assertSame($response->json()['data']['period'], 'quarterly');
        // static::assertSame($response->json()['data']['status'], 'completed');

        $response = $this->getJson(
            '/api/subscriptions'
        );

        $response->assertSuccessful();
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'package' => [
                            'name_en',
                            'name_ar',
                        ],
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
