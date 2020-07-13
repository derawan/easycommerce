<?php

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

// Route::middleware(['auth:api','cors','return-json'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => ['cors', 'return-json']], function () {
    // public routes
    Route::post('/login', 'Api\AuthController@login')->name('login.api');
    Route::post('/register','Api\AuthController@register')->name('register.api');

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/logout', 'Api\AuthController@logout')->name('logout.api');
        Route::post('/user', function (Request $request) {
            return $request->user();
        })->name('user.profile');
    });

});
