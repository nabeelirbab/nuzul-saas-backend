<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Transaction;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class TransactionAPITest extends TestCase
{
    /**
     * As a Admin, I should be able to view pending transactions, so that I can know what is pending.
     */
    public function testAdminCanViewOrders()
    {
        $this->adminLogin();
        $order = Order::factory()->yearly()->create();
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
        $response->assertJsonCount(1, 'data');

        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
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
                        'transactions' => [
                            '*' => [
                                'id',
                                'order_id',
                                'total_amount',
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
        $this->adminLogin();
        $order = Order::factory()->yearly()->create();
        $transaction = Transaction::factory()->create(
            [
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
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
                    'total_amount',
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
