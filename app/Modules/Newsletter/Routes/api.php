<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Newsletter\Http\Controllers\NewsletterController;

// API routes for Newsletter module
Route::group(['prefix' => 'api', 'as' => 'api.', 'middleware' => 'user'], function () {
    Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {
        Route::post('create-newsletter', [NewsletterController::class, 'createNewsletter'])
            ->name('create.newsletter');
    });
});
