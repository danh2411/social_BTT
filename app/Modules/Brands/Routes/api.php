<?php

// API routes for User module
use App\Modules\Brands\Http\Controllers\BrandController;
use Illuminate\Support\Facades\Route;

// API routes for Newsletter module
Route::group(['prefix' => 'api', 'as' => 'api.', 'middleware' => 'user'], function () {
    Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {
        Route::post('create-brand', [BrandController::class, 'createBrand'])
            ->name('create.brand');
        Route::get('list-brand', [BrandController::class, 'listBrand'])
            ->name('list.brand');
    });
});

