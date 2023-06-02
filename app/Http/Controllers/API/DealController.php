<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Deals\DealStoreRequest;
use App\Http\Requests\Api\Deals\DealUpdateRequest;
use App\Http\Resources\DealResource;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Property;
use App\Models\Role;
use App\Models\TenantContact;
use App\Models\TenantUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Role::ADMIN === auth()->user()->role_id) {
            $tcs = Deal::paginate(100);
        } else {
            $m = TenantUser::where('user_id', auth()->user()->id)->where('tenant_id', tenant()->id)->first();

            if ($m) {
                $name = $request->input('name');
                if ($name) {
                    $tcs = Deal::where('tenant_id', tenant()->id)->whereRelation('contact', 'contact_name_by_tenant', 'LIKE', '%'.$name.'%')->paginate(100);
                } else {
                    $tcs = Deal::where('tenant_id', tenant()->id)->paginate(100);
                }
            } else {
                return response()->json([
                    'message' => 'You are not authorized.',
                    'errors' => [],
                ], 401);
            }
        }

        return DealResource::collection($tcs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(DealStoreRequest $request)
    {
        $d = Deal::create(
            [
                'tenant_contact_id' => $request->tenant_contact_id,
                'tenant_id' => tenant()->id,
                'category' => $request->category,
                'purpose' => $request->purpose,
                'type' => $request->type,
            ]
        );
        $d->refresh();

        return new DealResource($d);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeWebsiteDeal(Request $request)
    {
        // let's check if contact exist by mobile number.
        $c = Contact::where('mobile_number', $request->mobile_number)->first();

        if (null === $c) {
            $c = Contact::create(
                [
                    'name' => $request->name,
                    'email' => $request->email ? $request->email : null,
                    'mobile_number' => $request->mobile_number,
                    'gender' => $request->gender ? $request->gender : null,
                ]
            );

            $tc = TenantContact::create(
                [
                    'contact_name_by_tenant' => $request->name,
                    'contact_id' => $c->id,
                    'tenant_id' => tenant()->id,
                ]
            );
        } elseif (null !== $c) {
            $tc = TenantContact::create(
                [
                    'contact_name_by_tenant' => $request->name,
                    'contact_id' => $c->id,
                    'tenant_id' => tenant()->id,
                ]
            );
        }

        $p = Property::find($request->property_id);
        $d = Deal::create(
            [
                'tenant_contact_id' => $tc->id,
                'tenant_id' => tenant()->id,
                'category' => $p->category,
                'purpose' => 'sell' === $p->purpose ? 'buy' : 'rent',
                'type' => $p->type,
                'property_id' => $p->id,
            ]
        );

        $d->refresh();

        return new DealResource($d);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Deal $deal)
    {
        return new DealResource($deal);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(DealUpdateRequest $request, Deal $deal)
    {
        $deal->property_id = $request->property_id;
        $deal->member_id = $request->sales_person_id;
        $deal->stage = $request->stage;
        $deal->min_price = $request->min_price;
        $deal->max_price = $request->max_price;
        $deal->min_area = $request->min_area;
        $deal->max_area = $request->max_area;
        $deal->bedrooms = $request->bedrooms;
        $deal->bathrooms = $request->bathrooms;
        $deal->facade = $request->facade;
        $deal->districts()->sync($request->get('districts_ids'));

        // rent
        if ('rent' === $deal->purpose) {
            $deal->rent_period = $request->rent_period;
        }

        // sell
        if ('sell' === $deal->purpose) {
            $deal->rent_period = null;
        }

        // land
        if ('land' === $deal->type) {
            $deal->bedrooms = null;
            $deal->bathrooms = null;
            $deal->is_kitchen_installed = false;
            $deal->is_ac_installed = false;
            $deal->is_furnished = false;
        }

        $deal->update();

        return new DealResource($deal);
    }

    public function dealsTotal()
    {
        $d = Deal::where('tenant_id', tenant()->id)->count();

        return response()->json([
            'data' => $d,
        ], 200);
    }

    public function dealsByStage()
    {
        $new = Deal::where('tenant_id', tenant()->id)->where('stage', 'new')->count();
        $visit = Deal::where('tenant_id', tenant()->id)->where('stage', 'visit')->count();
        $negotiation = Deal::where('tenant_id', tenant()->id)->where('stage', 'negotiation')->count();
        $won = Deal::where('tenant_id', tenant()->id)->where('stage', 'won')->count();
        $lost = Deal::where('tenant_id', tenant()->id)->where('stage', 'lost')->count();

        return response()->json([
            'data' => [
                'new' => $new,
                'visit' => $visit,
                'negotiation' => $negotiation,
                'won' => $won,
                'lost' => $lost,
            ],
        ], 200);
    }

    public function dealsGrowth()
    {
        $date = Carbon::now();
        $array = [];
        for ($i = 1; $i <= 12; ++$i) {
            $count = Deal::where('tenant_id', tenant()->id)->whereMonth('created_at', $i)
                ->whereYear('created_at', 2023)
                ->count()
            ;
            $array[$date->month($i)->format('F')] = $count;
        }

        return response()->json([
            'data' => $array,
        ], 200);
    }
}
