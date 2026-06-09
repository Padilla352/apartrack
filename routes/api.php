<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\ApartmentController;
use App\Http\Controllers\Api\ExploreController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\HelpController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/register/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/register/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/explore', [ExploreController::class, 'index']);
    Route::get('/apartments', [ApartmentController::class, 'index']);
    Route::get('/apartments/{id}', [ApartmentController::class, 'show']);
    Route::post('/help/feedback', [HelpController::class, 'storeFeedback']);

    // Social login
    Route::post('/auth/google', [SocialAuthController::class, 'googleLogin']);
    Route::post('/auth/facebook', [SocialAuthController::class, 'facebookLogin']);

    // Protected routes (auth:sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
        Route::post('/profile/notifications', [ProfileController::class, 'updateNotifications']);
        Route::delete('/profile', [ProfileController::class, 'destroy']);
        Route::get('/chat/{ownerId}/messages', [ChatController::class, 'getMessages']);
        Route::post('/chat/{ownerId}/messages', [ChatController::class, 'sendMessage']);
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    });
});