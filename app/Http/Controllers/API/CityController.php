<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CityStoreRequest;
use App\Http\Requests\Api\CityUpdateRequest;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Models\Role;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(City::class, 'city', [
            'except' => ['index'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        if (auth()->check() && Role::ADMIN === auth()->user()->role_id) {
            $cities = City::all();
        } else {
            $cities = City::where('active', true)->get();
        }

        return CityResource::collection($cities);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return CityResource
     */
    public function store(CityStoreRequest $request)
    {
        if ($city = City::create($request->toArray())) {
            return new CityResource($city);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(City $city)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return CityResource
     */
    public function update(CityUpdateRequest $request, City $city)
    {
        $city->name_ar = $request->name_ar;
        $city->name_en = $request->name_en;
        $city->update();

        return new CityResource($city);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
    }
}
