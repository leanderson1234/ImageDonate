<?php

use App\Http\Controllers\Api\v1\PhotoController;
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
  
Route::group(['prefix'=> 'v1'],function(){
    Route::post('auth','Api\v1\AuthApiController@authenticate');
    //Route::post('user','Api\v1\UserController@store');
});


Route::group([
    'prefix' => 'v1',
    'namespace' => 'Api\v1',
    ],function (){
        
    Route::apiResource('user','UserController');
        Route::group(['middleware' => 'auth:api'], function(){
            Route::apiResource('photo',"PhotoController");
            Route::apiResource('coment','ComentController');

            Route::get('authenticated/user','AuthApiController@getAuthenticatedUser');
            Route::post('authenticated/user/refresh','AuthApiController@refreshToken'); 
           
           // Route::get('user/{id}/photos','UserController@photos');
           // Route::get('photo/{id}/coments','PhotoController@coments');
        });
});
 