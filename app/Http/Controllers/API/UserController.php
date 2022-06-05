<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendSMSRequest;
use App\Http\Requests\Api\VerifyRequest;
use App\Models\SmsVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    public function generateSMS(SendSMSRequest $request)
    {
        $verification = SmsVerification::query()->where(function ($query) use ($request) {
            return $query->where('mobile_number', $request->get('mobile_number'));
        })->whereBetween('created_at', [Carbon::now()->addSeconds(-3), Carbon::now()])->exists();

        abort_if($verification, 403, __('You cannot send verification now'));

        $verify = new SmsVerification([
            'mobile_number' => $request->get('mobile_number'),
            'code' => app()->environment(['dev', 'local', 'testing']) ? '1111' : rand(1000, 9999),
        ]);

        $verify->save();

        return response()->json($verify);
    }

    public function verify(VerifyRequest $request)
    {
        $phone = SmsVerification::whereBetween('created_at', [Carbon::now()->addSeconds(-3), Carbon::now()])->where('mobile_number', $request->mobile_number)->latest()->first();

        if (!$phone) {
            return response()->json(['Message' => 'Wrong phone number or code expired, request another code.'], 422);
        }

        if (null !== $phone->token) {
            return response()->json(['Message' => 'Code expired, request another code.'], 422);
        }

        if ('3' === $phone->attempts) {
            return response()->json(['Message' => 'Attempts exceeded, request another code.'], 422);
        }

        $sms = SmsVerification::whereBetween('created_at', [Carbon::now()->addSeconds(-15), Carbon::now()])->where('mobile_number', $request->mobile_number)->where('code', $request->code)->latest()->first();

        if ($sms) {
            $token = Hash::make(random_bytes(64));
            $sms->token = $token;
            $sms->update();

            return response()->json(['token' => $token], 200);
        }

        if ($phone->attempts < 3) {
            $phone->attempts = $phone->attempts + 1;
            $phone->update();

            return response()->json(['Message' => 'Wrong code.'], 422);
        }
    }
}
