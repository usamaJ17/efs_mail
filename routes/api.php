<?php

use App\Http\Controllers\API\EmailController;
use App\Http\Controllers\API\OtpController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('get-access-token', [UserController::class, 'getAccessToken']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::group(['middleware' => 'admin'], function (){
        Route::post('users/create', [UserController::class, 'store']);
        Route::put('users/{user}', [UserController::class, 'update']);
        Route::delete('users/{user}', [UserController::class, 'destroy']);

        Route::get('emails', [EmailController::class, 'index']);
        Route::post('emails/send', [EmailController::class, 'send']);

        Route::get('otp/send/{user}', [OtpController::class, 'send']);
        Route::post('otp/verify', [OtpController::class, 'verify']);
    });
});
