<?php

declare(strict_types=1);

use App\Http\Controllers\API\InvitationController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\TransactionController;
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
                Route::post('/', [OrderController::class, 'store']);
                Route::put('/{order}/cancel', [OrderController::class, 'cancel']);
            });

            Route::group(['prefix' => 'transactions'], function () {
                Route::put('/{transaction}/accept', [TransactionController::class, 'accept']);
            });

            Route::group(['prefix' => 'subscriptions'], function () {
                Route::get('/', [SubscriptionController::class, 'index']);
            });

            Route::group(['prefix' => 'invites'], function () {
                Route::get('/', [SubscriptionController::class, 'index']);
            });

            Route::group(['prefix' => 'invitations'], function () {
                Route::get('/', [InvitationController::class, 'tenantInvitations']);
                Route::post('/', [InvitationController::class, 'store']);
                Route::put('/{invitation}/cancel', [InvitationController::class, 'cancel']);
            });
        });
    });
});
