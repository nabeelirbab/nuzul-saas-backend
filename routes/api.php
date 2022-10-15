<?php

use App\Http\Controllers\API\CityController;
use App\Http\Controllers\API\CountryController;
use App\Http\Controllers\API\InvitationController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\TenantController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;
use App\Models\Payment;
use Illuminate\Http\Request;
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

Route::post('/save', function (Request $request) {
    return Payment::create(['object' => $request->collect()]);
});

Route::get('/payment', [OrderController::class, 'paymentHandler']);

// Route::get('/payment', function (Request $request) {
//     return redirect('/home/dashboard');
// });

Route::group(['middleware' => ['TokenIsValid']], function () {
    Route::post('register', 'AuthController@register');
    Route::post('reset-password', 'AuthController@resetPassword');
});

Route::get('countries', [CountryController::class, 'index']);
Route::get('cities', [CityController::class, 'index']);
Route::get('packages', 'PackageController@index');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'me'], function () {
        Route::get('/', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'update']);
    });

    Route::group(['prefix' => 'countries'], function () {
        Route::post('/', [CountryController::class, 'store']);
        Route::put('/{country}', [CountryController::class, 'update']);
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', [OrderController::class, 'index']);
    });

    Route::group(['prefix' => 'cities'], function () {
        Route::post('/', [CityController::class, 'store']);
        Route::put('/{city}', [CityController::class, 'update']);
    });

    Route::group(['prefix' => 'packages'], function () {
        Route::post('/', [PackagesController::class, 'store']);
        Route::put('/{package}', [PackagesController::class, 'update']);
    });

    Route::group(['prefix' => 'transactions'], function () {
        Route::put('/{transaction}/accept', [TransactionController::class, 'accept']);
    });

    Route::group(['prefix' => 'invitations'], function () {
        Route::get('/', [InvitationController::class, 'index']);
        Route::put('/{invitation}/accept', [InvitationController::class, 'accept']);
        Route::put('/{invitation}/decline', [InvitationController::class, 'decline']);
    });

    Route::group(['prefix' => 'workspaces'], function () {
        Route::get('/', [UserController::class, 'getUserWorkspaces']);
        Route::get('/{tenant}', [TenantController::class, 'show']);
    });
});
