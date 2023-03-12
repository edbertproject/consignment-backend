<?php

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

Route::group(['middleware' => 'verify_xendit_token'], function () {
    Route::post('xendit/invoice', 'XenditController@invoice')->name('xendit.invoice');
    Route::post('xendit/virtual-account', 'XenditController@virtualAccount')->name('xendit.virtual-account');
    Route::post('xendit/virtual-account/update', 'XenditController@virtualAccountUpdate')->name('xendit.virtual-account-update');
});

Route::group(['namespace' => 'Auth'], function () {
    Route::post('register', 'RegisterController@store')->name('register');
    Route::post('register/verify', 'RegisterController@verify')->name('register.verify');
    Route::post('forgot-password', 'ForgotPasswordController@sendResetLinkEmail')->name('send-reset-link-email');
    Route::post('reset-password', 'ResetPasswordController@reset')->name('reset-password');
    Route::post('create-password', 'CreatePasswordController@createPassword')->name('create-password');
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['prefix' => 'account'], function () {
        Route::get('/', 'AccountController@account')->name('account.index');
        Route::post('/', 'AccountController@update')->name('account.update');
        Route::post('/update-password', 'AccountController@updatePassword')->name('account.update-password');
        Route::post('/logout', 'AccountController@revoke')->name('account.logout');

        Route::group(['prefix' => 'notification'], function () {
            Route::get('/','NotificationController@index')->name('index');
            Route::get('/{id}', 'NotificationController@show')->name('show');
            Route::post('/read-all', 'NotificationController@readAll')->name('read-all');
        });
    });

    Route::group(['namespace' => 'Public'], function () {
        Route::apiResource('cart', 'CartsController')->parameters(['cart' => 'id'])->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('order', 'OrdersController')->parameters(['order' => 'id'])->except(['destroy']);

        Route::apiResource('payment-method', 'PaymentMethodsController')->parameters(['payment-method' => 'id'])->only(['index', 'show']);

        Route::post('shipping/calculate', ['uses' => 'ShippingsController@calculate']);
    });

    Route::group(['prefix' => 'admin', 'middleware' => ['ensure_not_role:Public|Partner']], function () {
        Route::group(['prefix' => 'select'], function () {
            Route::get('/product-categories', ['uses' => 'ProductCategoriesController@select', 'middleware' => ['permission:read product category']]);
            Route::get('/provinces', ['uses' => 'ProvincesController@select']);
            Route::get('/cities', ['uses' => 'CitiesController@select']);
            Route::get('/districts', ['uses' => 'DistrictsController@select']);
            Route::get('/roles', ['uses' => 'RolesController@select', 'middleware' => ['permission:read role']]);
            Route::get('/permissions', ['uses' => 'PermissionsController@select', 'middleware' => ['permission:read role']]);
        });

        Route::get('/user-public', ['uses' => 'UserPublicController@index', 'middleware' => ['permission:read user public']]);
        Route::get('/user-public/{id}', ['uses' => 'UserPublicController@show', 'middleware' => ['permission:read user public']]);

        Route::get('/user-partner', ['uses' => 'UserPartnerController@index', 'middleware' => ['permission:read user partner']]);
        Route::post('/user-partner', ['uses' => 'UserPartnerController@store', 'middleware' => ['permission:write user partner']]);
        Route::get('/user-partner/{id}', ['uses' => 'UserPartnerController@show', 'middleware' => ['permission:read user partner']]);
        Route::put('/user-partner/{id}', ['uses' => 'UserPartnerController@update', 'middleware' => ['permission:write user partner']]);
        Route::delete('/user-partner/{id}', ['uses' => 'UserPartnerController@destroy', 'middleware' => ['permission:delete user partner']]);
        Route::put('/user-partner/status/{id}', ['uses' => 'UserPartnerController@updateStatus', 'middleware' => ['permission:write user partner']]);

        Route::get('/user-internal', ['uses' => 'UserInternalController@index', 'middleware' => ['permission:read user internal']]);
        Route::post('/user-internal', ['uses' => 'UserInternalController@store', 'middleware' => ['permission:write user internal']]);
        Route::get('/user-internal/{id}', ['uses' => 'UserInternalController@show', 'middleware' => ['permission:read user internal']]);
        Route::put('/user-internal/{id}', ['uses' => 'UserInternalController@update', 'middleware' => ['permission:write user internal']]);
        Route::delete('/user-internal/{id}', ['uses' => 'UserInternalController@destroy', 'middleware' => ['permission:delete user internal']]);

        Route::get('/roles', ['uses' => 'RolesController@index', 'middleware' => ['permission:read role']]);
        Route::post('/role', ['uses' => 'RolesController@store', 'middleware' => ['permission:write role']]);
        Route::get('/role/{id}', ['uses' => 'RolesController@show', 'middleware' => ['permission:read role']]);
        Route::put('/role/{id}', ['uses' => 'RolesController@update', 'middleware' => ['permission:write role']]);
        Route::delete('/role/{id}', ['uses' => 'RolesController@destroy', 'middleware' => ['permission:delete role']]);

        Route::get('/product-categories', ['uses' => 'ProductCategoriesController@index', 'middleware' => ['permission:read product category']]);
        Route::post('/product-category', ['uses' => 'ProductCategoriesController@store', 'middleware' => ['permission:write product category']]);
        Route::get('/product-category/{id}', ['uses' => 'ProductCategoriesController@show', 'middleware' => ['permission:read product category']]);
        Route::put('/product-category/{id}', ['uses' => 'ProductCategoriesController@update', 'middleware' => ['permission:write product category']]);
        Route::delete('/product-category/{id}', ['uses' => 'ProductCategoriesController@destroy', 'middleware' => ['permission:delete product category']]);

        Route::get('/products', ['uses' => 'ProductsController@index', 'middleware' => ['permission:read product']]);
        Route::post('/product', ['uses' => 'ProductsController@store', 'middleware' => ['permission:write product']]);
        Route::get('/product/{id}', ['uses' => 'ProductsController@show', 'middleware' => ['permission:read product']]);
        Route::put('/product/{id}', ['uses' => 'ProductsController@update', 'middleware' => ['permission:write product']]);
        Route::delete('/product/{id}', ['uses' => 'ProductsController@destroy', 'middleware' => ['permission:delete product']]);
        Route::put('/product/status/{id}', ['uses' => 'ProductsController@updateStatus', 'middleware' => ['permission:write product']]);
    });
});
