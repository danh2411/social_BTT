<?php

// API routes for User module
use Illuminate\Support\Facades\Route;
use App\Modules\User\Http\Controllers\UserController;

Route::group(['prefix' => 'api/front', 'as' => 'api.'], function(){
    Route::group(['prefix' => 'v1', 'as' => 'v1.'], function(){
Route::get('check-ticket', [UserController::class, 'test'])->name('check.ticket');
    });
});
