<?php

use Botble\Base\Facades\AdminHelper;
use Botble\SocialLogin\Facades\SocialService;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\SocialLogin\Http\Controllers'], function () {
    AdminHelper::registerRoutes(function () {
        Route::group(['prefix' => 'settings'], function () {
            Route::get('social-login', [
                'as' => 'social-login.settings',
                'uses' => 'Settings\SocialLoginSettingController@edit',
            ]);

            Route::put('social-login', [
                'as' => 'social-login.settings.update',
                'uses' => 'Settings\SocialLoginSettingController@update',
                'permission' => 'social-login.settings',
            ]);
        });
    });

    Route::group(['middleware' => ['web', 'core']], function () {
        Route::get('auth/{provider}', [
            'as' => 'auth.social',
            'uses' => 'SocialLoginController@redirectToProvider',
        ])->whereIn('provider', SocialService::getProviderKeys());

        Route::get('auth/callback/{provider}', [
            'as' => 'auth.social.callback',
            'uses' => 'SocialLoginController@handleProviderCallback',
        ])->whereIn('provider', SocialService::getProviderKeys());
    });
});
