<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Package;
use App\Models\Transaction;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class OrderAPITest extends TestCase
{
    /**
     * As a Company Owner, I should be able to select an available package and a period to make an order, so that I it could create a subscription.
     */
    public function testOwnerCanCreateAnOrderForTrial()
    {
        $package = Package::factory()->create(
            [
                'name_ar' => 'Gold-trial',
                'name_en' => 'الذهبية-تجربة',
                'price_monthly' => 0,
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
                'period' => 'monthly',
                'payment_method' => 'bank_transfer',
            ]
        );

        $response->assertSuccessful();
        $response->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'package_price_monthly',
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

        static::assertSame($response->json()['data']['package_price_monthly'], 0);
        static::assertSame($response->json()['data']['package_price_yearly'], 0);
        static::assertSame($response->json()['data']['package_tax'], 0);
        static::assertSame($response->json()['data']['tax_amount'], 0);
        static::assertSame($response->json()['data']['total_amount'], 0);
        static::assertSame($response->json()['data']['period'], 'monthly');
        static::assertSame($response->json()['data']['status'], 'completed');
    }

    public function testOwnerCanNOTCreateMultiplePendingOrders()
    {
        $package = Package::factory()->create(
            [
                'name_ar' => 'Gold-trial',
                'name_en' => 'الذهبية-تجربة',
                'price_monthly' => 0,
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

        $order = Order::factory()->yearly()->create(
            [
                'tenant_id' => $tenant->id,
                'package_id' => $package->id,
                'period' => 'monthly',
            ]
        );
        Transaction::factory()->create(
            [
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
            ]
        );

        $response = $this->postJson(
            '/api/orders',
            [
                'package_id' => $package->id,
                'period' => 'monthly',
                'payment_method' => 'bank_transfer',
            ]
        );

        $response->assertUnprocessable();
    }

    public function testOwnerCanNOTCreateOrdersWhenYouTheyHaveActiveSubscription()
    {
        $package = Package::factory()->create(
            [
                'name_ar' => 'Gold-trial',
                'name_en' => 'الذهبية-تجربة',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'tax' => 0,
                'status' => 'published',
                'is_trial' => true,
            ]
        );

        $user = $this->companyAccountLogin();
        $tenant = $user->tenants()->first();

        $tenantId = $user->tenants()->first()->id;

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $order = Order::factory()->yearly()->create(
            [
                'package_id' => $package->id,
                'period' => 'monthly',
                'tenant_id' => $tenantId,
            ]
        );
        Transaction::factory()->create(
            [
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
            ]
        );

        $response = $this->postJson(
            '/api/orders',
            [
                'package_id' => $package->id,
                'period' => 'monthly',
                'tenant_id' => $tenantId,
                'payment_method' => 'bank_transfer',
            ]
        );

        $response->assertUnprocessable();
    }

    public function testOwnerCanCancelPendingOrder()
    {
        $package = Package::factory()->create(
            [
                'name_ar' => 'Gold',
                'name_en' => 'الذهبية',
                'price_monthly' => 100,
                'price_yearly' => 100,
                'tax' => 0.15,
                'status' => 'published',
                'is_trial' => false,
            ]
        );

        $user = $this->companyAccountLogin();
        $tenant = $user->tenants()->first();

        $tenantId = $user->tenants()->first()->id;

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $order = Order::factory()->yearly()->create(
            [
                'package_id' => $package->id,
                'period' => 'monthly',
                'tenant_id' => $tenantId,
            ]
        );
        Transaction::factory()->create(
            [
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
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
        $package = Package::factory()->create(
            [
                'name_ar' => 'Gold',
                'name_en' => 'الذهبية',
                'price_monthly' => 100,
                'price_yearly' => 100,
                'tax' => 0.15,
                'status' => 'published',
                'is_trial' => false,
            ]
        );

        $user = $this->companyAccountLogin();
        $tenant = $user->tenants()->first();

        $tenantId = $user->tenants()->first()->id;

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://'.$tenant->domains[0]['domain']);

        $order = Order::factory()->yearly()->create(
            [
                'package_id' => $package->id,
                'period' => 'monthly',
                'tenant_id' => $tenantId,
            ]
        );

        Transaction::factory()->create(
            [
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
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
