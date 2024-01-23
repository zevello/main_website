<?php

use Botble\Base\Facades\BaseHelper;
use Botble\RealEstate\Http\Controllers\CouponController;
use Botble\RealEstate\Http\Controllers\CustomFieldController;
use Botble\RealEstate\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\RealEstate\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group([
        'prefix' => BaseHelper::getAdminPrefix() . '/real-estate',
        'middleware' => 'auth',
    ], function () {
        Route::group(['prefix' => 'settings', 'as' => 'real-estate.settings.'], function () {
            Route::get('general', [
                'as' => 'general',
                'uses' => 'Settings\GeneralSettingController@edit',
            ]);

            Route::put('general', [
                'as' => 'general.update',
                'uses' => 'Settings\GeneralSettingController@update',
                'permission' => 'real-estate.settings.general',
            ]);

            Route::get('currencies', [
                'as' => 'currencies',
                'uses' => 'Settings\CurrencySettingController@edit',
            ]);

            Route::put('currencies', [
                'as' => 'currencies.update',
                'uses' => 'Settings\CurrencySettingController@update',
                'permission' => 'real-estate.settings.currencies',
            ]);

            Route::get('accounts', [
                'as' => 'accounts',
                'uses' => 'Settings\AccountSettingController@edit',
            ]);

            Route::put('accounts', [
                'as' => 'accounts.update',
                'uses' => 'Settings\AccountSettingController@update',
                'permission' => 'real-estate.settings.accounts',
            ]);

            Route::get('invoices', [
                'as' => 'invoices',
                'uses' => 'Settings\InvoiceSettingController@edit',
            ]);

            Route::put('invoices', [
                'as' => 'invoices.update',
                'uses' => 'Settings\InvoiceSettingController@update',
                'permission' => 'real-estate.settings.invoices',
            ]);

            Route::get('invoice-template', [
                'as' => 'invoice-template',
                'uses' => 'Settings\InvoiceTemplateSettingController@edit',
            ]);

            Route::put('invoice-template', [
                'as' => 'invoice-template.update',
                'uses' => 'Settings\InvoiceTemplateSettingController@update',
                'permission' => 'real-estate.settings.invoice-template',
            ]);

            Route::prefix('invoice-template')->name('invoice-template.')->group(function () {
                Route::post('reset', [
                    'as' => 'reset',
                    'uses' => 'Settings\InvoiceTemplateSettingController@reset',
                    'permission' => 'invoice.index',
                ]);

                Route::get('preview', [
                    'as' => 'preview',
                    'uses' => 'Settings\InvoiceTemplateSettingController@preview',
                    'permission' => 'invoice.index',
                ]);
            });
        });

        Route::group(['prefix' => 'properties', 'as' => 'property.'], function () {
            Route::resource('', 'PropertyController')
                ->parameters(['' => 'property']);

            Route::post('duplicate-property/{id}', [
                'as' => 'duplicate-property',
                'uses' => 'DuplicatePropertyController@__invoke',
                'permission' => 'property.edit',
            ]);
        });

        Route::group(['prefix' => 'projects', 'as' => 'project.'], function () {
            Route::resource('', 'ProjectController')
                ->parameters(['' => 'project']);
        });

        Route::group(['prefix' => 'property-features', 'as' => 'property_feature.'], function () {
            Route::resource('', 'FeatureController')
                ->parameters(['' => 'property_feature']);
        });

        Route::group(['prefix' => 'investors', 'as' => 'investor.'], function () {
            Route::resource('', 'InvestorController')
                ->parameters(['' => 'investor']);
        });

        Route::group(['prefix' => 'consults', 'as' => 'consult.'], function () {
            Route::resource('', 'ConsultController')
                ->parameters(['' => 'consult'])
                ->except(['create', 'store']);
        });

        Route::group(['prefix' => 'categories', 'as' => 'property_category.'], function () {
            Route::resource('', 'CategoryController')
                ->parameters(['' => 'category']);

            Route::put('update-tree', [
                'as' => 'update-tree',
                'uses' => 'CategoryController@updateTree',
                'permission' => 'property_category.edit',
            ]);
        });

        Route::group(['prefix' => 'facilities', 'as' => 'facility.'], function () {
            Route::resource('', 'FacilityController')
                ->parameters(['' => 'facility']);
        });

        Route::group(['prefix' => 'accounts', 'as' => 'account.'], function () {
            Route::resource('', 'AccountController')
                ->parameters(['' => 'account']);

            Route::get('list', [
                'as' => 'list',
                'uses' => 'AccountController@getList',
                'permission' => 'account.index',
            ]);

            Route::post('credits/{id}', [
                'as' => 'credits.add',
                'uses' => 'TransactionController@postCreate',
                'permission' => 'account.edit',
            ])->wherePrimaryKey();
        });

        Route::group(['prefix' => 'packages', 'as' => 'package.'], function () {
            Route::resource('', 'PackageController')
                ->parameters(['' => 'package']);
        });

        Route::group(['prefix' => 'reviews', 'as' => 'review.'], function () {
            Route::resource('', 'ReviewController')->parameters(['' => 'review'])->only(['index', 'destroy']);
        });

        Route::prefix('custom-fields')->name('real-estate.custom-fields.')->group(function () {
            Route::resource('', CustomFieldController::class)->parameters(['' => 'custom-field']);

            Route::get('info', [
                'as' => 'get-info',
                'uses' => 'CustomFieldController@getInfo',
                'permission' => false,
            ]);
        });

        Route::group(['prefix' => 'invoices', 'as' => 'invoices.'], function () {
            Route::resource('', 'InvoiceController')->parameters(['' => 'invoice'])->except(['edit', 'update']);
            Route::get('{id}', [InvoiceController::class, 'show'])
                ->name('show')
                ->wherePrimaryKey();
            Route::get('{id}/generate', [InvoiceController::class, 'generate'])
                ->name('generate')
                ->wherePrimaryKey();
        });

        Route::group(['prefix' => 'import/properties', 'as' => 'import-properties.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'PropertyImportController@index',
            ]);

            Route::post('/', [
                'as' => 'store',
                'uses' => 'PropertyImportController@store',
                'permission' => 'import-properties.index',
            ]);

            Route::post('download-template', [
                'as' => 'download-template',
                'uses' => 'PropertyImportController@downloadTemplate',
                'permission' => 'import-properties.index',
            ]);
        });

        Route::group(['prefix' => 'import/projects', 'as' => 'import-projects.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'ProjectImportController@index',
            ]);

            Route::post('/', [
                'as' => 'store',
                'uses' => 'ProjectImportController@store',
                'permission' => 'import-projects.index',
            ]);

            Route::post('download-template', [
                'as' => 'download-template',
                'uses' => 'ProjectImportController@downloadTemplate',
                'permission' => 'import-projects.index',
            ]);
        });

        Route::group(['prefix' => 'export/properties', 'as' => 'export-properties.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'ExportPropertyController@index',
                'permission' => 'export-properties.index',
            ]);

            Route::post('/', [
                'as' => 'index.post',
                'uses' => 'ExportPropertyController@export',
                'permission' => 'export-properties.index',
            ]);
        });

        Route::group(['prefix' => 'export/projects', 'as' => 'export-projects.'], function () {
            Route::get('/', [
                'as' => 'index',
                'uses' => 'ExportProjectController@index',
                'permission' => 'export-projects.index',
            ]);

            Route::post('/', [
                'as' => 'index.post',
                'uses' => 'ExportProjectController@export',
                'permission' => 'export-projects.index',
            ]);
        });

        Route::group(['prefix' => 'coupons', 'as' => 'coupons.'], function () {
            Route::resource('', CouponController::class)
                ->parameters(['' => 'coupon']);

            Route::post('generate-coupon', [
                'as' => 'generate-coupon',
                'uses' => 'CouponController@generateCouponCode',
                'permission' => 'coupons.index',
            ]);

            Route::delete('deletes', [
                'as' => 'deletes',
                'uses' => 'CouponController@deletes',
                'permission' => 'coupons.destroy',
            ]);
        });
    });
});
