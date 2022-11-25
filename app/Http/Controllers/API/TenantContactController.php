<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TenantContactResource;
use App\Models\TenantContact;
use Illuminate\Http\Request;

class TenantContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
    public function show(TenantContact $tenantContact)
    {
        return new TenantContactResource($tenantContact);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(TenantContact $tenantContact)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateTenantContact(Request $request, TenantContact $tenantContact)
    {
        $tenantContact->contact->gender = $request->gender;
        $tenantContact->contact->update();
        $tenantContact->contact_name_by_tenant = $request->name;
        $tenantContact->is_property_buyer = $request->is_property_buyer;
        $tenantContact->is_property_owner = $request->is_property_owner;
        $tenantContact->district_id = $request->district_id;
        $tenantContact->update();

        return new TenantContactResource($tenantContact);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(TenantContact $tenantContact)
    {
    }
}
