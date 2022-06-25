<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CountryStoreRequest;
use App\Http\Requests\Api\CountryUpdateRequest;
use App\Http\Resources\CountryCollection;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\Models\Role;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Country::class, 'country', [
            'except' => ['index'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return CountryCollection
     */
    public function index()
    {
        if (auth()->check() && Role::ADMIN === auth()->user()->role_id) {
            $countries = Country::all();
        } else {
            $countries = Country::where('active', '1')->get();
        }

        return new CountryCollection($countries);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return CountryResource
     */
    public function store(CountryStoreRequest $request)
    {
        if ($country = Country::create($request->validated())) {
            return new CountryResource($country);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return CountryResource
     */
    public function update(CountryUpdateRequest $request, Country $country)
    {
        $country->name_ar = $request->name_ar;
        $country->name_en = $request->name_en;
        $country->update();

        return new CountryResource($country);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Country $country)
    {
    }
}
