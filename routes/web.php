<?php

use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [CommentController::class, 'frontend'])->name('frontend');
Route::get('/cms', [CommentController::class, 'cms'])->name('cms');

Route::prefix('api')->name('api.')->group(function () {
    Route::prefix('cms')->name('cms.')->group(function () {
        Route::get('/all', [CommentController::class, 'all_cms'])->name('all_cms');
        Route::delete('/delete', [CommentController::class, 'delete'])->name('delete');
        // Route::patch('/approve', [CommentController::class, 'approve'])->name('approve');
        // Route::patch('/reject', [CommentController::class, 'reject'])->name('reject');
    });
    Route::prefix('frontend')->name('frontend.')->group(function () {
        Route::get('/all', [CommentController::class, 'all'])->name('all');
        Route::post('/reply', [CommentController::class, 'reply'])->name('reply');
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
