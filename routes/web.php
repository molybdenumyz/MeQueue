<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

include 'custom/user.php';

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test','TestController@test');

Route::post('/order','QueueController@addOrder');

Route::put('/order','QueueController@updateOrderStatus');

Route::get('/order','QueueController@getOrder');

Route::delete('/order/{orderId}','QueueController@deleteOrder');