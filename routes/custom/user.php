<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 17/6/30
 * Time: 下午11:43
 */

Route::post('/user/register','UserController@register');
Route::post('/user/login','UserController@login');