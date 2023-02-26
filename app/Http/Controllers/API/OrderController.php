<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Orders\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\TransactionResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderSubscription;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Transaction;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Role::ADMIN === auth()->user()->role_id) {
            $orders = Order::paginate(100);
        } else {
            $orders = Order::whereIn('tenant_id', auth()->user()->tenants->pluck('id'))->paginate(100);
        }

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {
        $tenant = Tenant::find(tenant()->id);

        // check if the tenant has an active subscription or not
        // tenant has
        if ('0' !== (string) $tenant->subscriptions()->active()->count()) {
            $tenant = Tenant::find(tenant()->id);

            $trialSubscription = Subscription::where([['is_trial', true], ['tenant_id', tenant()->id]])->first();
            if ($request->is_trial && $trialSubscription) {
                return response()->json([
                    'message' => 'You can not start a trial more than once.',
                    'errors' => [
                    ],
                ], 422);
            }

            // check if the tenant has an active trial subscription or not
            if ('0' !== (string) $tenant->subscriptions()->activeTrial()->count()) {
                if ($trialSubscription) {
                    return response()->json([
                        'message' => 'You can not upgrade at the moment, try again later.',
                        'errors' => [
                        ],
                    ], 422);
                }
            }

            // if yes, get the order period tenant.activeSubscription.order.period
        // get the qty wanted for the request order
        // get the price based on the remaining days based on the period of the subscription
        // create new order
        // add the products into the order_product
        // calculate the total and update order total amounts
        // confirm the order
        // create the transaction
        // if the transaction successful
        // update order status
        // add the order intro order_subscription.
        } else {
            // tenant doesn't have
            // if the user doesn't have active subscription

            // check if the the order is trial.
            if ($request->is_trial) {
                // check if the customer had trials before
                $trialSubscription = Subscription::where([['is_trial', true], ['tenant_id', tenant()->id]])->first();
                if ($trialSubscription) {
                    return response()->json([
                        'message' => 'You can not start a trial more than once.',
                        'errors' => [
                        ],
                    ], 422);
                }

                $p = Product::where('id', 1)->first();

                // create new order
                $orderData =
                        [
                            'tenant_id' => $tenant->id,
                            'type' => 'subscription_trial',
                            'status' => 'completed',
                        ];

                $order = Order::create($orderData);
                $totalAmountWithoutTax = 0;
                $totalAmountWithTax = 0;
                // add the products into the order_product

                for ($i = 1; $i <= 2; ++$i) {
                    $op = OrderProduct::create([
                        'order_id' => $order->id,
                        'product_id' => 1,
                        'product_original_price' => 0,
                        'product_sale_price' => 0,
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'product_tax_percentage' => 0,
                        'product_tax_amount' => 0,
                        'total_amount_without_tax' => 0,
                        'total_amount_with_tax' => 0,
                    ]);
                }

                // calculate the total and update order total amounts
                $order->total_amount_without_tax = $totalAmountWithoutTax;
                $order->total_amount_with_tax = $totalAmountWithTax;
                $order->save();

                // confirm the order
                // create the transaction
                $transaction = Transaction::create([
                    'tenant_id' => $order->tenant_id,
                    'order_id' => $order->id,
                    'total_amount_with_tax' => $totalAmountWithTax,
                    'status' => 'approved',
                    'payment_method' => 'online',
                ]);

                // if the transaction successful
                // update order status
                // create a new subscription
                $subscription = Subscription::create(
                    [
                        'tenant_id' => $transaction->order->tenant_id,
                        'start_date' => now(),
                        'end_date' => now()->addDays(30),
                        'status' => 'active',
                        'is_trial' => true,
                    ]
                );
                // add the order into order_subscription.
                OrderSubscription::create(['order_id' => $order->id, 'subscription_id' => $subscription->id]);
            } else {
                // validate if products are not private and published
                foreach ($request->products as $product) {
                    $p = Product::where('id', $product['product_id'])->first();

                    if (true === (bool) $p->is_private) {
                        return response()->json([
                            'message' => 'You are not allowed.',
                            'errors' => [],
                        ], 422);
                    }
                    if ('published' !== $p->status) {
                        return response()->json([
                            'message' => 'You are not allowed.',
                            'errors' => [],
                        ], 422);
                    }
                    if ('quarterly' === $request->period) {
                        $request->type = 'subscription_quarterly';
                    }
                    if ('yearly' === $request->period) {
                        $request->type = 'subscription_yearly';
                    }
                }
                // create new order
                $orderData =
                        [
                            'tenant_id' => $tenant->id,
                            'type' => $request->type,
                            'status' => 'pending_payment',
                        ];

                $order = Order::create($orderData);
                $totalAmountWithoutTax = 0;
                $totalAmountWithTax = 0;
                // add the products into the order_product
                foreach ($request->products as $product) {
                    $p = Product::where('id', $product['product_id'])->first();
                    $originalPrice = 0;

                    // get the price based on the selected period of the subscription
                    if ('quarterly' === $request->period) {
                        $originalPrice = $p->price_quarterly_recurring * 3;
                    }

                    if ('yearly' === $request->period) {
                        $originalPrice = $p->price_yearly_recurring * 12;
                    }

                    // get the qty wanted for the request order
                    for ($i = 1; $i <= (int) $product['qty']; ++$i) {
                        $op = OrderProduct::create([
                            'order_id' => $order->id,
                            'product_id' => $product['product_id'],
                            'product_original_price' => $originalPrice,
                            'product_sale_price' => $originalPrice,
                            'discount_percentage' => 0,
                            'discount_amount' => 0,
                            'product_tax_percentage' => $p->tax_percentage,
                            'product_tax_amount' => $originalPrice * ($p->tax_percentage / 100),
                            'total_amount_without_tax' => $originalPrice,
                            'total_amount_with_tax' => $originalPrice + ($originalPrice * ($p->tax_percentage / 100)),
                        ]);

                        $totalAmountWithoutTax = $totalAmountWithoutTax + $op->total_amount_without_tax;
                        $totalAmountWithTax = $totalAmountWithTax + $op->total_amount_with_tax;
                    }
                }

                // calculate the total and update order total amounts
                $order->total_amount_without_tax = $totalAmountWithoutTax;
                $order->total_amount_with_tax = $totalAmountWithTax;
                $order->save();
                // confirm the order
                // create the transaction
                $transaction = Transaction::create([
                    'tenant_id' => $order->tenant_id,
                    'order_id' => $order->id,
                    'total_amount_with_tax' => $totalAmountWithTax,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                ]);
            }
        }

        return new OrderResource($order);
    }

    public function paymentHandler(Request $request)
    {
        \Moyasar\Moyasar::setApiKey(env('MOYASAR_KEY'));

        $paymentService = new \Moyasar\Providers\PaymentService();

        $payment = $paymentService->fetch($request->id);
        // dd($payment);

        if ('paid' === $payment->status) {
            // if payment succeeded
            // get the order id
            // check if the order is pending payment.
            // check if transaction id is pending.
            // check the order total amount equals amount
            // validate order and transaction
            // create subscription
            $o = Order::find($payment->metadata['order_id']);
            if ('pending_payment' === $o->status) {
                if ((float) ($payment->amount / 100) === $o->total_amount_with_tax) {
                    $t = Transaction::find($payment->metadata['transaction_id']);
                    if ('pending' === $t->status) {
                        $t->status = 'approved';
                        $t->update();
                        $o->status = 'completed';
                        $o->update();
                        if ('subscription_quarterly' === $o->type) {
                            $months = 3;
                        }
                        if ('subscription_yearly' === $o->type) {
                            $months = 12;
                        }
                        $s = Subscription::create(['tenant_id' => $o->tenant_id, 'start_date' => now(), 'end_date' => now()->addMonths($months), 'status' => 'active', 'is_trial' => false]);
                        // add the order into order_subscription.
                        OrderSubscription::create(['order_id' => $o->id, 'subscription_id' => $s->id]);
                    }
                }
            }
        }

        if ('failed' === $payment->status) {
            // check if payment failed
            // update transaction status to declined
            // store the response into response
            // store payment id to reference_number
            $o = Order::find($payment->metadata['order_id']);
            $t = Transaction::find($payment->metadata['transaction_id']);
            if ('pending' === $t->status) {
                $t->status = 'declined';
                $t->update();
                $o->status = 'canceled';
                $o->update();
            }
        }

        Payment::create(['object' => $payment->toJson()]);

        return redirect()->away($payment->metadata['redirect_url']);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTransactions(Order $order)
    {
        return TransactionResource::collection($order->transactions->paginate(100));
    }

    public function cancel(Order $order)
    {
        // check if requester belongs to the tenant, then check if requester is owner.
        $user = $order->tenant->users->where('id', auth()->user()->id)->first();
        if (Role::ADMIN === auth()->user()->role_id || $user && Role::COMPANY_OWNER === (string) $user->pivot->company_role_id) {
            // check if the transaction is pending
            if ('pending_payment' === $order->status) {
                $order->status = 'canceled';
                $order->transactions()->update(['status' => 'canceled']);
                $order->update();
            } else {
                return response()->json([
                    'message' => 'You can only cancel pending orders',
                    'errors' => [],
                ], 422);
            }
        } else {
            return response()->json([
                'message' => 'You are not authorized to cancel the order',
                'errors' => [],
            ], 403);
        }

        return new OrderResource($order);
    }
}
