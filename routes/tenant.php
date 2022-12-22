<?php

declare(strict_types=1);

use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\DealController;
use App\Http\Controllers\API\InvitationController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PropertyController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\TenantContactController;
use App\Http\Controllers\API\TenantUploadController;
use App\Http\Controllers\API\TenantUserController;
use App\Http\Controllers\API\TransactionController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::group([
    'prefix' => 'api',
], function () {
    Route::middleware([
        'api',
        InitializeTenancyByDomain::class,
        PreventAccessFromCentralDomains::class,
    ])->group(function () {
        Route::group(['middleware' => ['auth:sanctum']], function () {
            // Route::get('/', function () {
            //     return 'This is your saas application. The id of the current tenant is '.tenant('id');
            // });
            // dd(tenant());
            Route::group(['prefix' => 'orders'], function () {
                Route::get('/', [OrderController::class, 'index']);
                Route::get('/{order}', [OrderController::class, 'show']);
                Route::post('/', [OrderController::class, 'store']);
                Route::get('{id}/payment-handler', [OrderController::class, 'paymentHandler']);
                Route::put('/{order}/cancel', [OrderController::class, 'cancel']);
            });

            Route::group(['prefix' => 'transactions'], function () {
                Route::get('/', [TransactionController::class, 'tenantTransactions']);
            });

            Route::group(['prefix' => 'subscriptions'], function () {
                Route::get('/', [SubscriptionController::class, 'index']);
                Route::get('/active', [SubscriptionController::class, 'activeSubscription']);
            });

            Route::group(['prefix' => 'invitations'], function () {
                Route::get('/', [InvitationController::class, 'tenantInvitations']);
                Route::post('/', [InvitationController::class, 'store']);
                Route::put('/{invitation}/cancel', [InvitationController::class, 'cancel']);
            });

            Route::group(['prefix' => 'contacts'], function () {
                Route::post('/', [ContactController::class, 'store']);
                Route::get('/', [ContactController::class, 'index']);
                Route::put('/{tenantContact}', [TenantContactController::class, 'updateTenantContact']);
                Route::get('/{tenantContact}', [TenantContactController::class, 'show']);
            });

            Route::group(['prefix' => 'deals'], function () {
                Route::post('/', [DealController::class, 'store']);
                Route::get('/', [DealController::class, 'index']);
                Route::put('/{deal}', [DealController::class, 'update']);
                Route::get('/{deal}', [DealController::class, 'show']);
            });

            Route::group(['prefix' => 'members'], function () {
                Route::get('/', [TenantUserController::class, 'tenantMembers']);
                Route::delete('/{member}', [TenantUserController::class, 'destroy']);
                Route::put('/{member}/change-role', [TenantUserController::class, 'changeRole']);
            });

            Route::group(['prefix' => 'properties'], function () {
                Route::get('/', [PropertyController::class, 'index']);
                Route::post('/', [PropertyController::class, 'store']);
                Route::put('/{property}', [PropertyController::class, 'update']);
                Route::get('/{property}', [PropertyController::class, 'show']);
                Route::put('/{property}/cover', [PropertyController::class, 'setCover']);
                Route::put('/{property}/images', [PropertyController::class, 'setImages']);
                Route::delete('/{property}/images', [PropertyController::class, 'removeImages']);
            });

            Route::group(['prefix' => 'uploads'], function () {
                Route::post('/', [TenantUploadController::class, 'requestPresignedURL']);
            });
        });
    });
});
