<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TenantContactResource;
use App\Models\Contact;
use App\Models\Role;
use App\Models\TenantContact;
use App\Models\TenantUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Role::ADMIN === auth()->user()->role_id) {
            $tcs = TenantContact::paginate(100);
        } else {
            $m = TenantUser::where('user_id', auth()->user()->id)->where('tenant_id', tenant()->id)->first();

            if ($m) {
                $name = $request->input('name');
                $type = $request->input('type');
                if ($name) {
                    $tcs = TenantContact::where('tenant_id', tenant()->id)->where('contact_name_by_tenant', 'LIKE', '%'.$name.'%');
                    if ('owner' === $type) {
                        $tcs = $tcs->where('is_property_owner', true);
                    }
                } else {
                    $tcs = TenantContact::where('tenant_id', tenant()->id);
                }
            } else {
                return response()->json([
                    'message' => 'You are not authorized.',
                    'errors' => [],
                ], 401);
            }
        }

        return TenantContactResource::collection($tcs->paginate(100));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // let's check if contact exist by mobile number.
        $c = Contact::where('mobile_number', $request->mobile_number)->first();

        // if contacts exists, do validation
        if (null !== $c) {
            $tc = TenantContact::where([['contact_id', $c->id], ['tenant_id', tenant()->id]])->first();
        }

        // if tc exists,
        if (null !== $c && null !== $tc) {
            return response()->json([
                'message' => 'Contact already exists.',
                'errors' => [],
            ], 422);
        }

        if (null === $c) {
            $c = Contact::create(
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'mobile_number' => $request->mobile_number,
                    'gender' => $request->gender ? $request->gender : null,
                ]
            );

            $tc = TenantContact::create(
                [
                    'contact_name_by_tenant' => $request->name,
                    'contact_id' => $c->id,
                    'tenant_id' => tenant()->id,
                    'is_property_buyer' => $request->is_property_buyer,
                    'is_property_owner' => $request->is_property_owner,
                    'city_id' => $request->city_id ? $request->city_id : null,
                ]
            );
        } elseif (null !== $c) {
            $tc = TenantContact::create(
                [
                    'contact_name_by_tenant' => $request->name,
                    'contact_id' => $c->id,
                    'tenant_id' => tenant()->id,
                    'is_property_buyer' => $request->is_property_buyer,
                    'is_property_owner' => $request->is_property_owner,
                    'city_id' => $request->city_id ? $request->city_id : null,
                ]
            );
        }

        $tc = TenantContact::where([['contact_id', $c->id], ['tenant_id', tenant()->id]])->first();

        return new TenantContactResource($tc);
    }

    public function clientsTotal()
    {
        $tcs = TenantContact::where('tenant_id', tenant()->id)->count();

        return response()->json([
            'data' => $tcs,
        ], 200);
    }

    public function clientsGrowth()
    {
        $date = Carbon::now();
        $array = [];
        for ($i = 1; $i <= 12; ++$i) {
            $count = TenantContact::where('tenant_id', tenant()->id)->whereMonth('created_at', $i)
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
