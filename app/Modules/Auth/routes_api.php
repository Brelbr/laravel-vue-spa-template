<?php

use Illuminate\Support\Facades\Route;

//->withoutMiddleware('api')
// Route::middleware('cleanapi')->post('auth/logout', 'LoginController@logout')->name('logout');
// Route::middleware('api')->post('auth/me', 'UserController@me')->name('me');

Route::middleware('api')->prefix('auth')->group(function () {
    Route::withoutMiddleware('auth:sanctum')->group(function () {
        Route::middleware('web')->withoutMiddleware('api')->post('weblogin', 'LoginController@login')->name('weblogin'); // для таго что бы работала нормальная авторизация для всех страниц
        Route::post('login', 'LoginController@login')->name('login');
        Route::post('register', 'RegisterController@register')->name('register');

        Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.reset-email');
        Route::post('password/reset', 'ResetPasswordController@reset')->name('password.reset');

        Route::post('email/verify/{user}', 'VerificationController@verify')->name('verification.verify');
        Route::post('email/resend', 'VerificationController@resend')->name('verification.resend');

    });

    Route::middleware('cleanapi')->withoutMiddleware('api')->post('logout', 'LoginController@logout')->name('logout');
    Route::post('me', 'UserController@me')->name('me');
});
