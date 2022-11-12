<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tenants\TenantUpdateRequest;
use App\Http\Resources\TenantResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantUser;

class TenantController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Tenant $tenant)
    {
        return new TenantResource($tenant);
    }

    public function update(TenantUpdateRequest $request, Tenant $tenant)
    {
        $m = TenantUser::where('user_id', auth()->user()->id)->where('tenant_id', $tenant->id)->first();

        if ($m && (int) Role::COMPANY_OWNER === (int) $m->role->id) {
            $tenant->name_ar = $request->name_ar;
            $tenant->name_en = $request->name_en;
            $tenant->update();

            return new TenantResource($tenant);
        }

        return response()->json([
            'message' => 'You are not authorized to update this workspace',
            'errors' => [],
        ], 401);
    }

    public function setDefault(Tenant $tenant)
    {
        $m = TenantUser::where('user_id', auth()->user()->id)->where('tenant_id', $tenant->id)->first();
        TenantUser::where('user_id', auth()->user()->id)->where('tenant_id', $tenant->id)->update(['is_default' => false]);
        $m->is_default = true;
        $m->update();

        return WorkspaceResource::collection(auth()->user()->tenants);
    }

    public function leave(Tenant $tenant)
    {
        $m = TenantUser::where('user_id', auth()->user()->id)->where('tenant_id', $tenant->id)->first();
        if ($m && (int) Role::COMPANY_OWNER !== (int) $m->role->id) {
            $m->delete();

            return WorkspaceResource::collection(auth()->user()->tenants);
        }

        return response()->json([
            'message' => 'Owners can not leave their own workspace.',
            'errors' => [],
        ], 422);
    }
}
