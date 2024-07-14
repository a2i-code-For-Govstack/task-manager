<?php


use Illuminate\Support\Facades\Route;



Route::post('client-login', [\App\Http\Controllers\API\LoginController::class, 'clientLogin']);

Route::group(['middleware' => ['auth.jwt']], function () {
    require __DIR__.'/v1-api.php';

    Route::group(['prefix' => 'notification'], function () {
        Route::post('authorization', [\App\Http\Controllers\PusherController::class, 'notificationAuthorization']);
    });


    Route::post('doptor/search-user', [\App\Http\Controllers\API\DoptorController::class, 'searchUser']);
});







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