<?php

// API routes for User module
use Illuminate\Support\Facades\Route;
use App\Modules\User\Http\Controllers\UserController;

Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
    Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {
        // Route tạo mới người dùng
        Route::post('create-user', [UserController::class, 'createUser'])->name('create.user');

        // Route lấy danh sách người dùng
        Route::get('list-users', [UserController::class, 'listUsers'])->name('list.users');

        // Route lấy thông tin người dùng theo ID
        Route::get('get-user/{id}', [UserController::class, 'getUserByID'])->name('get.user');

        // Route lấy thông tin người dùng theo ID
        Route::post('login', [UserController::class, 'login'])->name('login');

        Route::get('user', [UserController::class, 'getUserByToken'])->middleware('auth:api')->name('user.get');

    });
});
