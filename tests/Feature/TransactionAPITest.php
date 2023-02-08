<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Transaction;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class TransactionAPITest extends TestCase
{
    /**
     * As a Admin, I should be able to view pending transactions, so that I can know what is pending.
     */
    public function testAdminCanViewOrders()
    {
        $user = $this->companyAccountLogin();
        $tenant = $user->tenants()->first();
        $tenantId = $user->tenants()->first()->id;

        $this->adminLogin();
        $order = Order::factory()->create([
            'tenant_id' => $tenantId,
            'type' => 'subscription_trial',
            'status' => 'pending_payment',
            'total_amount_with_tax' => 0,
        ]);
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
        $response->assertJsonCount(1, 'data');

        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'total_amount_without_tax',
                        'total_amount_with_tax',
                        'type',
                        'status',
                        'created_at',
                        'updated_at',
                        'transactions',
                        'transactions' => [
                            '*' => [
                                'id',
                                'order_id',
                                'total_amount_with_tax',
                                'payment_method',
                                'response',
                                'reference_number',
                                'created_at',
                                'updated_at',
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * As a Admin, I should be able to accept a pending transaction, so that a subscription gets created.
     */
    public function testAdminCanAcceptPendingTransaction()
    {
        $user = $this->companyAccountLogin();
        $tenant = $user->tenants()->first();
        $tenantId = $user->tenants()->first()->id;

        $this->adminLogin();
        $order = Order::factory()->create([
            'tenant_id' => $tenantId,
            'type' => 'subscription_trial',
            'status' => 'pending_payment',
            'total_amount_with_tax' => 0,
        ]);
        $transaction = Transaction::factory()->create(
            [
                'tenant_id' => $tenant->id,
                'order_id' => $order->id,
                'total_amount_with_tax' => $order->total_amount_with_tax,
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
            ]
        );

        $response = $this->putJson(
            "/api/transactions/{$transaction->id}/accept",
            ['reference_number' => 'qwerty']
        );

        $response->assertSuccessful();
        $response->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'order_id',
                    'total_amount_with_tax',
                    'status',
                    'payment_method',
                    'response',
                    'reference_number',
                    'created_at',
                    'updated_at',
                ],
            ]
        );
        static::assertSame($response->json()['data']['status'], 'approved');
        static::assertSame($response->json()['data']['reference_number'], 'qwerty');
    }
}
