<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Models\Role;
use App\Models\TenantUser;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Role::ADMIN === auth()->user()->role_id) {
            $p = Property::paginate(100);
        } else {
            $m = TenantUser::where('user_id', auth()->user()->id)->where('tenant_id', tenant()->id)->first();

            if ($m) {
                $id = $request->input('id');
                if ($id) {
                    $p = Property::where('tenant_id', tenant()->id)->where('id', 'LIKE', '%'.$id.'%')->paginate(100);
                } else {
                    $p = Property::where('tenant_id', tenant()->id)->paginate(100);
                }
            } else {
                return response()->json([
                    'message' => 'You are not authorized.',
                    'errors' => [],
                ], 401);
            }
        }

        return PropertyResource::collection($p);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if listing_purpose sell:
        // make rent_price_monthly,rent_price_quarterly,rent_price_half_yearly and rent_price_yearly = null
        if ('sell' === $request->purpose) {
            $request->rent_price_monthly = null;
            $request->rent_price_quarterly = null;
            $request->rent_price_annually = null;
            $request->rent_price_semi_annually = null;
        }

        // if listing_purpose rent:
        // make selling_price = null
        if ('rent' === $request->purpose) {
            $request->selling_price = null;
        }

        // if type not building_apartment or villa_apartment or office
        // make floor_number null
        if ('office' !== $request->type || 'building_apartment' !== $request->type || 'villa_apartment' !== $request->type) {
            $request->unit_floor_number = null;
        }

        // if type land
        // everything should be null expect for street_width, plot_size, price, longitude, latitude, district_id, listing_images_urls, and cover_image_url
        if ('land' === $request->type) {
            $request->year_built = null;
            $request->is_furnished = null;
            $request->is_parking_shade = null;
            $request->is_ac_installed = null;
            $request->is_kitchen_installed = null;
            $request->facade = null;
            $request->parking_spots = null;
            $request->gardens = null;
            $request->kitchens = null;
            $request->balconies = null;
            $request->pools = null;
            $request->elevators = null;
            $request->basement_rooms = null;
            $request->storage_rooms = null;
            $request->mulhaq_rooms = null;
            $request->driver_rooms = null;
            $request->maid_rooms = null;
            $request->majlis_rooms = null;
            $request->living_rooms = null;
            $request->dining_rooms = null;
            $request->bathrooms = null;
            $request->bedrooms = null;
            $request->unit_floor_number = null;
            $request->number_of_floors = null;
            $request->area = null;
        }

        // $li = $request->toArray();
        // $li['user_id'] = auth()->user()->id;

        // if ($listing = Listing::create($li)) {
        //     if (isset($li['listing_images_urls'])) {
        //         foreach ($li['listing_images_urls'] as $item) {
        //             $upload = Upload::create(['url' => $item]);
        //             $upload->reference()->associate($listing);
        //             $upload->save();
        //         }
        //     }
        // }

        $p = $request->toArray();
        $p['tenant_id'] = tenant()->id;

        $p = Property::create($p);
        $p->refresh();

        return new PropertyResource($p);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Property $property)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Property $property)
    {
    }
}
