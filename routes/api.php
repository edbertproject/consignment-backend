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

Route::group(['namespace' => 'Auth'], function () {
    Route::post('register', 'RegisterController@store')->name('register');
    Route::post('register/verify', 'RegisterController@verify')->name('register.verify');
    Route::post('forgot-password', 'ForgotPasswordController@sendResetLinkEmail')->name('send-reset-link-email');
    Route::post('reset-password', 'ResetPasswordController@reset')->name('reset-password');
    Route::post('create-password', 'CreatePasswordController@createPassword')->name('create-password');
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['prefix' => 'notification'], function () {
        Route::get('/','NotificationController@index')->name('index');
        Route::get('/{id}', 'NotificationController@show')->name('show');
        Route::post('/read-all', 'NotificationController@readAll')->name('read-all');
    });

    Route::group(['prefix' => 'account'], function () {
        Route::get('/', 'AccountController@account')->name('account.index');
        Route::post('/', 'AccountController@update')->name('account.update');
        Route::post('/update-password', 'AccountController@updatePassword')->name('account.update-password');
        Route::post('/logout', 'AccountController@revoke')->name('account.logout');
        Route::get('/permission', 'AccountController@permission')->name('account.permission');
        Route::get('/notification/read-all', 'AccountController@readAllNotification')->name('account.notification.read');
    });

    Route::group(['prefix' => 'admin', 'middleware' => ['role:Super Admin|Admin|Partner']], function () {

        Route::get('/product-categories', ['uses' => 'ProductCategoriesController@index', 'middleware' => ['permission:read product category']]);
        Route::post('/product-category', ['uses' => 'ProductCategoriesController@store', 'middleware' => ['permission:write product category']]);
        Route::get('/product-category/{id}', ['uses' => 'ProductCategoriesController@show', 'middleware' => ['permission:read product category']]);
        Route::put('/product-category/{id}', ['uses' => 'ProductCategoriesController@update', 'middleware' => ['permission:write product category']]);
        Route::delete('/product-category/{id}', ['uses' => 'ProductCategoriesController@destroy', 'middleware' => ['permission:delete product category']]);

    });
});
