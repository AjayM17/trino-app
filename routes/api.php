<?php

use Illuminate\Http\Request;

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
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'API\RegisterController@register');
Route::post('login', 'API\LoginController@login');
Route::post('updateProfile', 'API\LoginController@updateProfile');

Route::get('searchProduct/{data}', 'API\ProductController@searchProduct');

Route::middleware('auth:api')->group( function () {
	Route::resource('products', 'API\ProductController');
});
Route::middleware('auth:api')->group( function () {
	Route::resource('categories', 'API\CategoryController');
});
Route::middleware('auth:api')->group( function () {
	Route::resource('orders', 'API\OrdersController');
});
