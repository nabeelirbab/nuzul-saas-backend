<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Models\Role;
use App\Models\TenantUser;
use Illuminate\Http\Request;

class TenantUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tenantMembers()
    {
        $members = TenantUser::where('user_id', '<>', auth()->user()->id)->where('tenant_id', tenant()->id)->paginate(100);

        return MemberResource::collection($members);
    }

    public function destroy(TenantUser $member)
    {
        // TODO: check if requester is a tenant owner.
        // get the member tenant id
        // check if the requester belongs to members tenant.
        // check if the requester is an owner of the members tenant.
        if ((int) tenant()->id === (int) $member->tenant_id) {
            $m = TenantUser::where('user_id', auth()->user()->id)->where('tenant_id', tenant()->id)->first();
            if ((int) Role::COMPANY_OWNER === (int) $m->role->id) {
                return $member->delete();
            }
        }

        return response()->json([
            'message' => 'You are not authorized to remove this member.',
            'errors' => [],
        ], 401);
    }

    public function changeRole(TenantUser $member, Request $request)
    {
        // TODO: check if requester is a tenant owner.

        // should not change roles of self
        // get the member tenant id
        // check if the requester belongs to members tenant.
        // check if the requester is an owner of the members tenant.
        if ((int) tenant()->id === (int) $member->tenant_id) {
            $m = TenantUser::where('user_id', auth()->user()->id)->where('tenant_id', tenant()->id)->first();
            if ((int) Role::COMPANY_OWNER === (int) $m->role->id) {
                $member->company_role_id = $request->company_role_id;
                $member->update();

                return new MemberResource($member);
            }
        }

        return response()->json([
            'message' => 'You are not authorized to change the role of this member.',
            'errors' => [],
        ], 401);
    }
}
