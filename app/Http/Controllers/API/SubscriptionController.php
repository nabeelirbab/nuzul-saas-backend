<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use App\Models\Role;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Role::ADMIN === auth()->user()->role_id) {
            $subscriptions = Subscription::paginate(100);
        } else {
            // check the role of requester
            $subscriptions = Subscription::where('tenant_id', tenant()->id)->paginate(100);
        }

        return SubscriptionResource::collection($subscriptions);
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
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function activeSubscription()
    {
        $subscription = Subscription::where([['tenant_id', tenant()->id], ['status', 'active']])->first();
        if ($subscription) {
            return new SubscriptionResource($subscription);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(subscription $subscription)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, subscription $subscription)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(subscription $subscription)
    {
    }
}
