<?php

use App\Http\Controllers\API\CityController;
use App\Http\Controllers\API\CountryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('send-sms', 'UserController@generateSMS');
Route::post('verify-code', 'UserController@verify');

Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');

Route::get('countries', [CountryController::class, 'index']);
Route::get('cities', [CityController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('packages', 'PackageController@index');

    Route::group(['prefix' => 'countries'], function () {
        Route::post('/', [CountryController::class, 'store']);
        Route::put('/{country}', [CountryController::class, 'update']);
    });

    Route::group(['prefix' => 'cities'], function () {
        Route::post('/', [CityController::class, 'store']);
        Route::put('/{city}', [CityController::class, 'update']);
    });
});
