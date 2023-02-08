<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class OrderAPITest extends TestCase
{
    /**
     * As a Company Owner, I should be able to select an available products and a period to make an order, so that I it could create a subscription.
     */

    /**
     * As a Company Owner, I should be able to select an available products and a period to make an order, so that I it could create a subscription.
     */
    public function testOwnerCanCreateAnOrderForTrial()
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

        $p2 = Product::factory()->create(
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
        static::assertSame($response->json()['data']['total_amount_with_tax'], 0);
        static::assertSame($response->json()['data']['total_amount_without_tax'], 0);
        static::assertSame($response->json()['data']['type'], 'subscription_trial');
        static::assertSame($response->json()['data']['status'], 'completed');
    }

    /**
     * As a Company Owner, I should be able to select an available products and a period to make an order, so that I it could create a subscription.
     */
    public function testOwnerCanNotCreateAnOrderForMultiTrial()
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

        $p2 = Product::factory()->create(
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
        static::assertSame($response->json()['data']['total_amount_with_tax'], 0);
        static::assertSame($response->json()['data']['total_amount_without_tax'], 0);
        static::assertSame($response->json()['data']['type'], 'subscription_trial');
        static::assertSame($response->json()['data']['status'], 'completed');

        $response = $this->postJson(
            '/api/orders',
            [
                'is_trial' => true,
            ]
        );
        // not allowing multi trials
        $response->assertUnprocessable();
    }

    /**
     * As a Company Owner, I should be able to select an available products and a period to make an order, so that I it could create a subscription.
     */
    public function testOwnerCanNotCreateAnOrderForUpgrade()
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

        $p2 = Product::factory()->create(
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
        static::assertSame($response->json()['data']['total_amount_with_tax'], 0);
        static::assertSame($response->json()['data']['total_amount_without_tax'], 0);
        static::assertSame($response->json()['data']['type'], 'subscription_trial');
        static::assertSame($response->json()['data']['status'], 'completed');

        $response = $this->postJson(
            '/api/orders',
            [
                'products' => [
                    [
                        'product_id' => $p->id,
                        'qty' => 1,
                    ],
                    [
                        'product_id' => $p2->id,
                        'qty' => 2,
                    ],
                ],
                'period' => 'quarterly',
                'payment_method' => 'bank_transfer',
            ]
        );
        // not allowing upgrade
        $response->assertUnprocessable();
    }

    public function testOwnerCanCreateAPaidOrder()
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

        $p2 = Product::factory()->create(
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
                'products' => [
                    [
                        'product_id' => $p->id,
                        'qty' => 1,
                    ],
                    [
                        'product_id' => $p2->id,
                        'qty' => 2,
                    ],
                ],
                'period' => 'quarterly',
                'payment_method' => 'bank_transfer',
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
        static::assertSame($response->json()['data']['total_amount_with_tax'], 345);
        static::assertSame($response->json()['data']['total_amount_without_tax'], 300);
        static::assertSame($response->json()['data']['type'], 'subscription_quarterly');
        static::assertSame($response->json()['data']['status'], 'pending_payment');
    }

    // /**
    //  * As a Company Owner, I should be able to select an available package and a period to make an order, so that I it could create a subscription.
    //  */
    // public function testOwnerCanCreateAnOrderForTrial()
    // {
    //     $package = Package::factory()->create(
    //         [
    //             'name_ar' => 'Gold-trial',
    //             'name_en' => 'الذهبية-تجربة',
    //             'price_quarterly' => 0,
    //             'price_yearly' => 0,
    //             'tax' => 0,
    //             'status' => 'published',
    //             'is_trial' => true,
    //         ]
    //     );

    //     $user = $this->companyAccountLogin();
    //     $tenant = $user->tenants()->first();

    //     tenancy()->initialize($tenant);
    //     URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

    //     $response = $this->postJson(
    //         '/api/orders',
    //         [
    //             'package_id' => $package->id,
    //             'period' => 'quarterly',
    //             'payment_method' => 'bank_transfer',
    //         ]
    //     );

    //     $response->assertSuccessful();
    //     $response->assertJsonStructure(
    //         [
    //             'data' => [
    //                 'id',
    //                 'package_price_quarterly',
    //                 'package_price_yearly',
    //                 'package_tax',
    //                 'tax_amount',
    //                 'total_amount',
    //                 'period',
    //                 'status',
    //                 'created_at',
    //                 'updated_at',
    //             ],
    //         ]
    //     );

    //     static::assertSame($response->json()['data']['package_price_quarterly'], 0);
    //     static::assertSame($response->json()['data']['package_price_yearly'], 0);
    //     static::assertSame($response->json()['data']['package_tax'], 0);
    //     static::assertSame($response->json()['data']['tax_amount'], 0);
    //     static::assertSame($response->json()['data']['total_amount'], 0);
    //     static::assertSame($response->json()['data']['period'], 'quarterly');
    //     static::assertSame($response->json()['data']['status'], 'completed');
    // }

    public function testOwnerCanCancelPendingOrder()
    {
        $product = Product::factory()->create(
            [
                'type' => 'recurring',
                'name_ar' => 'مقعد - تجريبي',
                'name_en' => 'Seat - trial',
                'price' => 0,
                'price_monthly_recurring' => 0,
                'price_quarterly_recurring' => 0,
                'price_yearly_recurring' => 0,
                'tax_percentage' => 0,
                'status' => 'published',
                'is_private' => false,
            ]
        );

        $user = $this->companyAccountLogin();
        $tenant = $user->tenants()->first();

        $tenantId = $user->tenants()->first()->id;

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $order = Order::factory()->create(
            [
                'tenant_id' => $tenantId,
                'type' => 'subscription_trial',
                'status' => 'pending_payment',
                'total_amount_with_tax' => 0,
            ]
        );
        Transaction::factory()->create(
            [
                'tenant_id' => $tenant->id,
                'order_id' => $order->id,
                'total_amount_with_tax' => $order->total_amount_with_tax,
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
            ]
        );

        $response = $this->putJson(
            "/api/orders/{$order->id}/cancel"
        );

        $response->assertSuccessful();
    }

    public function testOwnerCanCViewOrders()
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

        $tenantId = $user->tenants()->first()->id;

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $order = Order::factory()->create(
            [
                'tenant_id' => $tenantId,
                'type' => 'subscription_trial',
                'status' => 'pending_payment',
                'total_amount_with_tax' => 0,
            ]
        );

        Transaction::factory()->create(
            [
                'tenant_id' => $tenant->id,
                'order_id' => $order->id,
                'total_amount_with_tax' => $order->total_amount_with_tax,
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
            ]
        );

        $response = $this->getJson(
            '/api/orders'
        );

        $response->assertSuccessful();
    }
}
