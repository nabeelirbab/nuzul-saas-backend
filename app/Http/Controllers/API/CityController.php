<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CityStoreRequest;
use App\Http\Requests\Api\CityUpdateRequest;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Store a newly created resource in storage.
     *
     * @return CityResource
     */
    public function store(CityStoreRequest $request)
    {
        if ($city = City::create($request->validated())) {
            return new CityResource($city);
        }
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
        $city->region_id = $request->region_id;
        $city->country_id = $request->country_id;
        $city->name_ar = $request->name_ar;
        $city->name_en = $request->name_en;
        $city->update();

        return new CityResource($city);
    }

    public function getCityByLatLong(Request $request)
    {
        $shops = DB::table('cities');

        $shops = $shops->select('*', DB::raw('6371 * acos(cos(radians('.$request->lat.'))
                                    * cos(radians(latitude)) * cos(radians(longitude) - radians('.$request->long.'))
                                    + sin(radians('.$request->lat.')) * sin(radians(latitude))) AS distance'));
        $shops = $shops->having('distance', '>', 10);
        $shops = $shops->orderBy('distance', 'asc');

        return $shops->get();

        return new CityResource($shops->first());
    }
}
