<?php

// API routes for User module
use App\Modules\Resources\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

// API routes for Newsletter module
Route::group(['prefix' => 'api', 'as' => 'api.', 'middleware' => 'user'], function () {
    Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {

        Route::post('create-resource', [ResourceController::class, 'createResource'])
            ->name('create.resource');

        Route::get('list-resource', [ResourceController::class, 'listResource'])
            ->name('list.resource');
    });
});

