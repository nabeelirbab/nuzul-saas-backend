<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Invitations\StoreInvitationRequest;
use App\Http\Resources\InvitationResource;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\TenantUser;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invitations = Invitation::where('mobile_number', auth()->user()->mobile_number)->paginate(100);

        return InvitationResource::collection($invitations);
    }

    public function tenantInvitations()
    {
        if (tenant()->users->where('user_id', auth()->user()->id)) {
            $invitations = Invitation::paginate(100);

            return InvitationResource::collection($invitations);
        }
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
    public function store(StoreInvitationRequest $request)
    {
        // check if invitee has invitation pending.
        $hasInvitation = Invitation::where([
            ['mobile_number', $request->mobile_number],
            ['status', 'pending'],
            ['tenant_id', tenant()->id],
        ])->first();

        if ($hasInvitation) {
            return response()->json([
                'message' => 'There is a already pending invitation.',
                'errors' => [],
            ], 422);
        }

        $invite = Invitation::create(
            [
                'mobile_number' => $request->mobile_number,
                'tenant_id' => tenant()->id,
                'company_role_id' => $request->role_id,
                'expires_at' => now()->addDays(2),
            ]
        );

        // send sms invitation to the person
        if (app()->environment(['production'])) {
            Unifonic::send(
                $request->mobile_number,
                'Your have been invited to join [company-name] team.
            تمت دعوتك من قبل فريق

            https://company.nuzul.app

            Invitation expires after 48 hours.
            تنتهي صلاحية الدعوة بعد 48 ساعة.

            Nuzul Business
            نزل للأعمال'
            );
        }

        return new InvitationResource($invite);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Invitation $invitation)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel(Invitation $invitation)
    {
        // TODO: check if it belongs to the company owner.

        // TODO: check if requester is a tenant owner.
        // get the member tenant id
        // check if the requester belongs to members tenant.
        // check if the requester is an owner of the members tenant.

        if ((int) tenant()->id === (int) $invitation->tenant_id) {
            $m = TenantUser::where('user_id', auth()->user()->id)->where('tenant_id', tenant()->id)->first();
            if ((int) Role::COMPANY_OWNER === (int) $m->role->id) {
                $invitation->status = 'canceled';
                $invitation->update();

                return new InvitationResource($invitation);
            }
        }

        return response()->json([
            'message' => 'You are not authorized to remove this member.',
            'errors' => [],
        ], 401);
    }

    public function decline(Invitation $invitation)
    {
        if (auth()->user()->mobile_number === $invitation->mobile_number && 'pending' === $invitation->status) {
            $invitation->status = 'declined';
            $invitation->update();

            return new InvitationResource($invitation);
        }

        return response()->json([
            'message' => 'You are not authorized to decline the invitation.',
            'errors' => [],
        ], 401);
    }

    public function accept(Invitation $invitation)
    {
        if (auth()->user()->mobile_number === $invitation->mobile_number && 'pending' === $invitation->status) {
            $invitation->status = 'accepted';
            $invitation->update();

            TenantUser::create([
                'user_id' => auth()->user()->id,
                'tenant_id' => $invitation->tenant_id,
                'company_role_id' => $invitation->company_role_id,
            ]);

            return new InvitationResource($invitation);
        }

        return response()->json([
            'message' => 'You are not authorized to accept the invitation.',
            'errors' => [],
        ], 401);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invitation $invitation)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invitation $invitation)
    {
    }
}
