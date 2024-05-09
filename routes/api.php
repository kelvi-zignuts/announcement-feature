<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AnnouncementController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\API\UserAnnouncementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


Route::post('/register', [RegisteredUserController::class, 'store']);

Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('announcements')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index']);
        // Route::get('create', [AnnouncementsController::class, 'create'])->name('announcements.create');
        Route::post('store', [AnnouncementController::class, 'store']);
        Route::post('view/{id}', [AnnouncementController::class, 'view']);
        Route::post('update/{id}', [AnnouncementController::class, 'update']);
        Route::post('delete/{id}', [AnnouncementController::class, 'destroy']);
    });

    Route::prefix('announcements')->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('/', [UserAnnouncementController::class, 'index']);
            Route::get('/detail/{id}', [UserAnnouncementController::class, 'show']);
        });
    });
});


// Route::get('/health', function (Request $request) {
//     return 'here';
// });