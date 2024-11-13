<?php

// API routes for User module
use Illuminate\Support\Facades\Route;
use App\Modules\User\Http\Controllers\UserController;
Route::get('/test', [UserController::class, 'test']);
