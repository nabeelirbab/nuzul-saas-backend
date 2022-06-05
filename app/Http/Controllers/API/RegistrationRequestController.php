<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegistrationRequestStoreRequest;
use App\Http\Requests\Api\RegistrationRequestUpdateRequest;
use App\Http\Resources\RegistrationRequestResource;
use App\Models\RegistrationRequest;
use App\Models\SmsVerification;

class RegistrationRequestController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(RegistrationRequest::class, 'registrationRequest', [
            'except' => ['store'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $registration_requests = RegistrationRequest::paginate(10);

        return RegistrationRequestResource::collection($registration_requests);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RegistrationRequestStoreRequest $data)
    {
        $mobileNumber = SmsVerification::where('token', $data['token'])->first()->mobile_number;
        $registrationRequestData = [
            'business_category_id' => $data['business_category_id'],
            'city_id' => $data['city_id'],
            'business_name' => $data['business_name'],
            'business_position' => $data['business_position'],
            'cr_number' => $data['cr_number'],
            'number_of_branches' => $data['number_of_branches'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'mobile_number' => $mobileNumber,
            'status' => 'pending',
        ];

        $registerRequest = RegistrationRequest::create($registrationRequestData);

        return new RegistrationRequestResource($registerRequest);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Models\RegistrationRequest $registerRequest
     *
     * @return RegistrationRequestResource
     */
    public function update(RegistrationRequestUpdateRequest $request, RegistrationRequest $registrationRequest)
    {
        $registrationRequest->business_category_id = $request->business_category_id;
        $registrationRequest->city_id = $request->city_id;
        $registrationRequest->business_name = $request->business_name;
        $registrationRequest->business_position = $request->business_position;
        $registrationRequest->cr_number = $request->cr_number;
        $registrationRequest->number_of_branches = $request->number_of_branches;
        $registrationRequest->full_name = $request->full_name;
        $registrationRequest->email = $request->email;
        $registrationRequest->mobile_number = $request->mobile_number;
        $registrationRequest->notes = $request->notes;

        $registrationRequest->update();

        return new RegistrationRequestResource($registrationRequest);
    }
}
