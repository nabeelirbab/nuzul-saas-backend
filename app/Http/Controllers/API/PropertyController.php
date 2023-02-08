<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Models\Role;
use App\Models\TenantUpload;
use App\Models\TenantUser;
use Carbon\Carbon;
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
        $request->unit_number = rand(3333, 9999999);
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
        return new PropertyResource($property);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Property $property)
    {
        // if listing_purpose sell:
        // make rent_price_monthly,rent_price_quarterly,rent_price_half_yearly and rent_price_yearly = null
        if ('sell' === $property->purpose) {
            $property->rent_price_monthly = null;
            $property->rent_price_quarterly = null;
            $property->rent_price_annually = null;
            $property->rent_price_semi_annually = null;
        }

        // if listing_purpose rent:
        // make selling_price = null
        if ('rent' === $property->purpose) {
            $property->selling_price = null;
        }

        // if type not building_apartment or villa_apartment or office
        // make floor_number null
        if ('office' !== $request->type || 'building_apartment' !== $request->type || 'villa_apartment' !== $request->type) {
            $property->unit_floor_number = null;
        }

        // if type land
        // everything should be null expect for street_width, plot_size, price, longitude, latitude, district_id, listing_images_urls, and cover_image_url
        if ('land' === $property->type) {
            $property->year_built = null;
            $property->is_furnished = null;
            $property->is_parking_shade = null;
            $property->is_ac_installed = null;
            $property->is_kitchen_installed = null;
            $property->facade = null;
            $property->parking_spots = null;
            $property->gardens = null;
            $property->kitchens = null;
            $property->balconies = null;
            $property->pools = null;
            $property->elevators = null;
            $property->basement_rooms = null;
            $property->storage_rooms = null;
            $property->mulhaq_rooms = null;
            $property->driver_rooms = null;
            $property->maid_rooms = null;
            $property->majlis_rooms = null;
            $property->living_rooms = null;
            $property->dining_rooms = null;
            $property->bathrooms = null;
            $property->bedrooms = null;
            $property->unit_floor_number = null;
            $property->number_of_floors = null;
            $property->area = null;
        }

        $property->unit_number = $request->unit_number;
        $property->tenant_contact_id = $request->tenant_contact_id;

        $property->published_on_website = $request->published_on_website;
        $property->published_on_app = $request->published_on_app;

        $property->length = $request->length;
        $property->width = $request->width;

        $property->year_built = $request->year_built;
        $property->street_width = $request->street_width;
        $property->selling_price = $request->selling_price;

        $property->availability_status = $request->availability_status;
        $property->availability_date = $request->availability_date;

        $property->rent_price_monthly = $request->rent_price_monthly;
        $property->rent_price_quarterly = $request->rent_price_quarterly;
        $property->rent_price_annually = $request->rent_price_annually;
        $property->rent_price_semi_annually = $request->rent_price_semi_annually;

        $property->area = $request->area;
        $property->longitude = $request->longitude;
        $property->latitude = $request->latitude;
        $property->number_of_floors = $request->number_of_floors;
        $property->unit_floor_number = $request->unit_floor_number;
        $property->bathrooms = $request->bathrooms;
        $property->bedrooms = $request->bedrooms;
        $property->dining_rooms = $request->dining_rooms;
        $property->living_rooms = $request->living_rooms;
        $property->majlis_rooms = $request->majlis_rooms;
        $property->maid_rooms = $request->maid_rooms;
        $property->driver_rooms = $request->driver_rooms;
        $property->mulhaq_rooms = $request->mulhaq_rooms;
        $property->is_furnished = $request->is_furnished;
        $property->is_parking_shade = $request->is_parking_shade;
        $property->is_ac_installed = $request->is_ac_installed;
        $property->is_kitchen_installed = $request->is_kitchen_installed;
        $property->facade = $request->facade;
        $property->parking_spots = $request->parking_spots;
        $property->gardens = $request->gardens;
        $property->kitchens = $request->kitchens;
        $property->balconies = $request->balconies;
        $property->pools = $request->pools;
        $property->elevators = $request->elevators;
        $property->basement_rooms = $request->basement_rooms;
        $property->storage_rooms = $request->storage_rooms;
        $property->cover_image_url = $request->cover_image_url;
        $property->district_id = $request->district_id;

        $property->update();
        $property->refresh();

        return new PropertyResource($property);
    }

    public function setCover(Request $request, Property $property)
    {
        $property->cover_image_url = $request->cover_image_url;

        $property->update();
        $property->refresh();

        return new PropertyResource($property);
    }

    public function setImages(Request $request, Property $property)
    {
        $li = $request->toArray();

        if (isset($li['images_urls'])) {
            foreach ($li['images_urls'] as $item) {
                $upload = TenantUpload::create(['url' => $item, 'tenant_id' => tenant()->id]);
                $upload->reference()->associate($property);
                $upload->save();
            }
        }

        return new PropertyResource($property->load('images'));
    }

    public function removeImages(Request $request, Property $property)
    {
        // TODO: Auth
        $li = $request->toArray();
        if (isset($li['images_urls'])) {
            foreach ($li['images_urls'] as $item) {
                $upload = TenantUpload::where(['url' => $item], ['tenant_id' => tenant()->id]);
                $upload->delete();
            }
        }

        return new PropertyResource($property->load('images'));
    }

    public function propertiesTotal()
    {
        $d = Property::where('tenant_id', tenant()->id)->count();

        return response()->json([
            'data' => $d,
        ], 200);
    }

    public function projectsTotal()
    {
        return response()->json([
            'data' => 0,
        ], 200);
    }

    public function propertiesGrowth()
    {
        $date = Carbon::now();
        $array = [];
        for ($i = 1; $i <= 12; ++$i) {
            $count = Property::where('tenant_id', tenant()->id)->whereMonth('created_at', $i)
                ->whereYear('created_at', 2023)
                ->count()
            ;
            $array[$date->month($i)->format('F')] = $count;
        }

        return response()->json([
            'data' => $array,
        ], 200);
    }
}
