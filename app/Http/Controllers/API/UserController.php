<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendSMSRequest;
use App\Http\Requests\Api\VerifyRequest;
use App\Models\SmsVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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

        abort_if($verification, 403, App::isLocale('en') ? 'You cannot send verification now, try again after 1 minute.' : 'لا يمكنك طلب الرمز في الوقت الحالي، الرجاء المحاولة بعد مرور دقيقة واحدة');

        $code = app()->environment(['dev', 'local', 'testing']) ? '1111' : rand(1000, 9999);
        $verify = new SmsVerification([
            'mobile_number' => $request->get('mobile_number'),
            'code' => $code,
        ]);

        $verify->save();

        if (app()->environment(['production'])) {
            Unifonic::send($request->mobile_number, 'Your code is: '.$code, 'Nuzul');
        }

        return response()->json($verify);
    }

    public function verify(VerifyRequest $request)
    {
        $phone = SmsVerification::where('mobile_number', $request->mobile_number)->latest()->first();

        if (!$phone) {
            switch (App::currentLocale()) {
                case 'ar':
                    $message = 'خطأ في رقم الهاتف او الرمز المدخل، الرجاء طلب رمز جديد.';

                    break;

                    default:
                    $message = 'Wrong phone number or code expired, request another code.';
            }

            return response()->json(['message' => $message], 422);
        }

        if (null !== $phone->token) {
            switch (App::currentLocale()) {
                case 'ar':
                    $message = 'انتهت صلاحية الرمز، الرجاء طلب رمز جديد.';

                    break;

                    default:
                    $message = 'Code expired, request another code.';
            }

            return response()->json(['message' => $message], 422);
        }

        if ('3' === $phone->attempts || 3 === $phone->attempts) {
            switch (App::currentLocale()) {
                case 'ar':
                    $message = 'انتهت عدد المحاولات، الرجاء طلب رمز جديد.';

                    break;

                    default:
                    $message = 'Attempts exceeded, request another code.';
            }

            return response()->json(['message' => $message], 422);
        }

        $sms = SmsVerification::where('mobile_number', $request->mobile_number)->where('code', $request->code)->latest()->first();

        if ($sms) {
            SmsVerification::where('mobile_number', $request->mobile_number)->update(['token' => null]);
            $token = Hash::make(random_bytes(64));
            $sms->token = $token;
            $sms->update();

            return response()->json(['token' => $token], 200);
        }

        if ($phone->attempts < 3) {
            $phone->attempts = $phone->attempts + 1;
            $phone->update();

            switch (App::currentLocale()) {
                case 'ar':
                    $message = 'الرمز المدخل غير صحيح.';

                    break;

                    default:
                    $message = 'Please provide correct code.';
            }

            return response()->json(['message' => $message], 422);
        }
    }
}
