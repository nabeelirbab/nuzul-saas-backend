<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Orders\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\TransactionResource;
use App\Models\Order;
use App\Models\Package;
use App\Models\Payment;
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
        // retrieve package
        $package = Package::find($request->package_id);
        $tenant = Tenant::find(tenant()->id);

        // check if the tenant has active subscription.
        if ('0' !== (string) $tenant->subscriptions()->active()->count()) {
            return response()->json([
                'message' => 'You already have an active subscription.',
                'errors' => [],
            ], 422);
        }

        // check if the tenant doesn't have a previous pending order.
        if ('0' !== (string) $tenant->orders->where('status', 'pending_payment')->count()) {
            return response()->json([
                'message' => 'You already have a pending order, to make a new order you must cancel it.',
                'errors' => [],
            ], 422);
        }

        // TODO: refactor check if package is published
        if ('published' === $package->status) {
            // TODO: refactor check if the package is trial
            if ($package->is_trial) {
                // TODO: refactor check if tenant had a trial before
                if (!$tenant->subscriptions()->trial()->count()) {
                    // check if payment of
                    $orderData =
                    [
                        'tenant_id' => $tenant->id,
                        'package_id' => $request->package_id,
                        'package_price_quarterly' => $package->price_quarterly,
                        'package_price_yearly' => $package->price_yearly,
                        'package_tax' => $package->tax,
                        'tax_amount' => 0,
                        'total_amount' => 0,
                        'period' => $request->period,
                        'status' => 'completed',
                    ];

                    $order = Order::create($orderData);

                    $transaction = Transaction::create([
                        'tenant_id' => $order->tenant_id,
                        'order_id' => $order->id,
                        'total_amount' => 0,
                        'status' => 'approved',
                        'payment_method' => 'none',
                    ]);

                    // if package is trial. Subscription should start instantly.
                    Subscription::create(
                        [
                            'tenant_id' => $transaction->order->tenant_id,
                            'package_id' => $transaction->order->package_id,
                            'start_date' => now(),
                            'end_date' => now()->addDays(14),
                            'status' => 'active',
                            'is_trial' => true,
                        ]
                    );
                } else {
                    return response()->json([
                        'message' => 'You can not subscribe to a trial package more than once.',
                        'errors' => [
                            'package_id' => [
                                'The provided package is incorrect.',
                            ],
                        ],
                    ], 422);
                }
            }

            // not trail
            if (!$package->is_trial) {
                $taxAmount = 'quarterly' === $request->period ? ($package->tax * $package->price_quarterly / 100) : ($package->tax * $package->price_yearly / 100);
                $totalAmount = 'quarterly' === $request->period ? ($package->tax * $package->price_quarterly / 100) + $package->price_quarterly : ($package->tax * $package->price_yearly / 100) + $package->price_yearly;

                $orderData =
                [
                    'tenant_id' => $tenant->id,
                    'package_id' => $request->package_id,
                    'package_price_quarterly' => $package->price_quarterly,
                    'package_price_yearly' => $package->price_yearly,
                    'package_tax' => $package->tax,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'period' => $request->period,
                    'status' => 'pending_payment',
                ];

                $order = Order::create($orderData);

                $transaction = Transaction::create([
                    'tenant_id' => $order->tenant_id,
                    'order_id' => $order->id,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                ]);
            }
        } else {
            abort(422, [
                'message' => 'Something went wrong.',
                'errors' => [
                    'package_id' => [
                        'The provided package is incorrect.',
                    ],
                ],
            ]);
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
                if ((float) ($payment->amount / 100) === $o->total_amount) {
                    $t = Transaction::find($payment->metadata['transaction_id']);
                    if ('pending' === $t->status) {
                        $t->status = 'approved';
                        $t->update();
                        $o->status = 'completed';
                        $o->update();
                        Subscription::create(['tenant_id' => $o->tenant_id, 'package_id' => $o->package_id, 'start_date' => now(), 'end_date' => now()->addMonths(3), 'status' => 'active', 'is_trial' => false]);
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
            }
            $order->update();
        } else {
            return response()->json([
                'message' => 'You are not authorized to cancel the order',
                'errors' => [],
            ], 403);
        }

        return new OrderResource($order);
    }
}
