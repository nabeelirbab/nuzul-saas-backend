<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TenantContactResource;
use App\Models\Contact;
use App\Models\Role;
use App\Models\TenantContact;
use App\Models\TenantUser;
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
                if ($name) {
                    $tcs = TenantContact::where('tenant_id', tenant()->id)->where('contact_name_by_tenant', 'LIKE', '%'.$name.'%')->paginate(100);
                } else {
                    $tcs = TenantContact::where('tenant_id', tenant()->id)->paginate(100);
                }
            } else {
                return response()->json([
                    'message' => 'You are not authorized.',
                    'errors' => [],
                ], 401);
            }
        }

        return TenantContactResource::collection($tcs);
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
                    'district_id' => $request->district_id ? $request->district_id : null,
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
                    'district_id' => $request->district_id ? $request->district_id : null,
                ]
            );
        }

        $tc = TenantContact::where([['contact_id', $c->id], ['tenant_id', tenant()->id]])->first();

        return new TenantContactResource($tc);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
    }
}
