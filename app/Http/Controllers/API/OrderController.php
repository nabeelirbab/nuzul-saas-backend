<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Orders\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\TransactionResource;
use App\Models\Order;
use App\Models\Package;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Transaction;

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

        // TODO: check if package is published
        if ('published' === $package->status) {
            // TODO: check if the package is trial
            if ($package->is_trial) {
                // TODO: check if tenant had a trial before
                if (!$tenant->subscriptions()->trial()->count()) {
                    $orderData =
                    [
                        'tenant_id' => $tenant->id,
                        'package_id' => $request->package_id,
                        'package_price_monthly' => $package->price_monthly,
                        'package_price_yearly' => $package->price_yearly,
                        'package_tax' => $package->tax,
                        'tax_amount' => 0,
                        'total_amount' => 0,
                        'period' => $request->period,
                        'status' => 'completed',
                    ];

                    $order = Order::create($orderData);

                    $transaction = Transaction::create([
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
                            'end_date' => ('monthly' === $transaction->order ? now()->addDays(30) : now()->addDays(365)),
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
                $taxAmount = 'monthly' === $request->period ? $package->tax * $package->price_monthly : $package->tax * $package->price_yearly;
                $totalAmount = 'monthly' === $request->period ? ($package->tax * $package->price_monthly) + $package->price_monthly : ($package->tax * $package->price_yearly) + $package->price_yearly;

                $orderData =
                [
                    'tenant_id' => $tenant->id,
                    'package_id' => $request->package_id,
                    'package_price_monthly' => $package->price_monthly,
                    'package_price_yearly' => $package->price_yearly,
                    'package_tax' => $package->tax,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'period' => $request->period,
                    'status' => 'pending_payment',
                ];

                $order = Order::create($orderData);

                $transaction = Transaction::create([
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
