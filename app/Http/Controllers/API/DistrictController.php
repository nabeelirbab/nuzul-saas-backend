<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DistrictResource;
use App\Models\District;
use App\Models\Role;

class DistrictController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(District::class, 'district', [
            'except' => ['index', 'show'],
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
            $district = District::all();
        } else {
            $city_id = request()->query('city_id');
            if ($city_id) {
                $district = District::where([['active', true], ['city_id', $city_id]])->get();
            } else {
                $district = District::where([['active', true]])->get();
            }
        }

        return DistrictResource::collection($district->load('city'));
    }
}
