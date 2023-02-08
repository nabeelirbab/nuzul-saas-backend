<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Role;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->check() && Role::ADMIN === auth()->user()->role_id) {
            $products = Product::all();
        } else {
            $products = Product::where('status', 'published')->get();
        }

        return ProductResource::collection($products);
    }
}
