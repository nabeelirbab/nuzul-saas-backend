<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\OrderSubscription;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tenantTransactions()
    {
        $transactions = Transaction::paginate(100);

        return TransactionResource::collection($transactions);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransactionRequest $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
    }

    public function accept(Transaction $transaction, Request $request)
    {
        // check if the transaction is pending
        $transaction->status = 'approved';
        $transaction->reference_number = $request->reference_number;
        $transaction->update();

        // TODO: check if order is not a subscription and handle the logic
        // start creating subscription
        $months = 0;
        if ('subscription_quarterly' === $transaction->order->type) {
            $months = 3;
        }
        if ('subscription_yearly' === $transaction->order->type) {
            $months = 12;
        }
        $s = Subscription::create(
            [
                'tenant_id' => $transaction->order->tenant_id,
                'start_date' => now(),
                'end_date' => now()->addDays($months),
                'status' => 'active',
                'is_trial' => false,
            ]
        );

        OrderSubscription::create(['order_id' => $transaction->order->id, 'subscription_id' => $s->id]);

        return new TransactionResource($transaction);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
    }
}
