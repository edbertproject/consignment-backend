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

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/test', function () {
        return 'test';
    });

    Route::group(['prefix' => 'admin', 'middleware' => ['role:Super Admin']], function () {

        Route::get('/product-categories', ['uses' => 'ProductCategoriesController@index', 'middleware' => ['permission:read product category']]);
        Route::post('/product-category', ['uses' => 'ProductCategoriesController@store', 'middleware' => ['permission:write product category']]);
        Route::get('/product-category/{id}', ['uses' => 'ProductCategoriesController@show', 'middleware' => ['permission:read product category']]);
        Route::put('/product-category/{id}', ['uses' => 'ProductCategoriesController@update', 'middleware' => ['permission:write product category']]);
        Route::delete('/product-category/{id}', ['uses' => 'ProductCategoriesController@destroy', 'middleware' => ['permission:delete product category']]);

    });
});
