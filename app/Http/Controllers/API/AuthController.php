<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRegisterRequest;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\SmsVerification;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required',
            'password' => 'required',
        ]);
        $user = User::query()->where([['mobile_number', $request->mobile_number]])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'mobile_number' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken(time());

        $loginArray = [
            'data' => [
                'token' => $token->plainTextToken,
                'email' => $user->email,
                'mobile_number' => $user->mobile_number,
                'role' => [
                    'role_id' => $user->role->id,
                    'name_ar' => $user->role->name_ar,
                    'name_en' => $user->role->name_en,
                ],
                'name' => $user->name,
                'workspaces' => $user->tenants->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'is_default' => (bool) $item->pivot->is_default,
                        'name_en' => $item->name_en,
                        'name_ar' => $item->name_ar,
                        'active' => $item->active,
                        'company_role' => [
                            'role_id' => $item->pivot->role->id,
                            'name_ar' => $item->pivot->role->name_ar,
                            'name_en' => $item->pivot->role->name_en,
                        ],
                        'domain' => Tenant::find($item->id)->domains->first()->domain,
                    ];
                }),
                'pending_invitations' => InvitationResource::collection($user->pendingInvitations),
            ],
        ];
        if (Role::COMPANY !== (string) $user->role_id) {
            unset($loginArray['data']['workspaces']);
        }

        return $loginArray;
    }

    public function register(UserRegisterRequest $request)
    {
        $mobileNumber = SmsVerification::where('token', $request->token)->first()->mobile_number;

        $userData = [
            'name' => $request['name'],
            'password' => bcrypt($request['password']),
            'mobile_number' => $mobileNumber,
            'email' => $request['email'],
            'role_id' => Role::COMPANY,
        ];

        $user = User::create($userData);

        $tenant = Tenant::create(
            [
                'name_en' => $request['name'],
                'name_ar' => $request['name'],
            ]
        );

        $tenant->users()->attach($user->id, ['company_role_id' => Role::COMPANY_OWNER, 'is_default', true]);

        $centralDomains = explode(',', env('CENTRAL_DOMAINS'));

        $tenant->domains()->create(['domain' => readable_random_string().$tenant->id.'.'.$centralDomains[1]]);

        $token = $user->createToken(time());

        $user = new UserResource($user);
        $user->additional(['data' => ['token' => $token->plainTextToken]]);

        SmsVerification::where('token', $request->token)->update(['token' => null]);

        $loginArray = [
            'data' => [
                'token' => $token->plainTextToken,
                'email' => $user->email,
                'mobile_number' => $user->mobile_number,
                'role' => [
                    'role_id' => $user->role->id,
                    'name_ar' => $user->role->name_ar,
                    'name_en' => $user->role->name_en,
                ],
                'name' => $user->name,
                'workspaces' => $user->tenants->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'is_default' => (bool) $item->pivot->is_default,
                        'name_en' => $item->name_en,
                        'name_ar' => $item->name_ar,
                        'active' => $item->active,
                        'company_role' => [
                            'role_id' => $item->pivot->role->id,
                            'name_ar' => $item->pivot->role->name_ar,
                            'name_en' => $item->pivot->role->name_en,
                        ],
                        'domain' => Tenant::find($item->id)->domains->first()->domain,
                    ];
                }),
                'pending_invitations' => InvitationResource::collection($user->pendingInvitations),
            ],
        ];

        return $loginArray;
    }

    public function logout()
    {
        $user = request()->user();
        // Revoke current user token
        $deleted = $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        if ($deleted) {
            return response()->json([
                'message' => 'Logged out successfully',
            ]);
        }
        abort(403, 'Something went wrong.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $userToken = SmsVerification::where('token', $request->token)->first();
        $user = User::where('mobile_number', $userToken->mobile_number)->first();

        $user->update(['password' => bcrypt($request['password'])]);
        $token = $user->createToken(time());

        $user = new UserResource($user);
        $user->additional(['data' => ['token' => $token->plainTextToken]]);
        SmsVerification::where('token', $request->token)->update(['token' => null]);

        return $user;
    }
}
