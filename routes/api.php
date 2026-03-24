<?php

use Illuminate\Support\Facades\Route;

// Authentication
Route::prefix('auth')->middleware(['auth.rate.limit'])->group(function () {
    Route::post('login', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'loginApi']);
    Route::post('register', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'registerApi']);
    Route::post('verify-email', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'verifyEmailApi']);
    Route::post('forgot-password', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'forgotPasswordApi']);
    Route::post('reset-password', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'resetPasswordApi']);
    Route::post('resend-otp', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'resendOtpApi']);
    Route::post('verify-otp', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'verifyOtpApi']);
});

// Recipes & Categories API
Route::get('home', [\App\Http\Controllers\API\RecipeApiController::class, 'home']);
Route::get('recipes', [\App\Http\Controllers\API\RecipeLibraryController::class, 'index']);
Route::get('recommendations/today', [\App\Http\Controllers\API\RecommendationController::class, 'today']);
Route::get('recipes/{slug}', [\App\Http\Controllers\API\RecipeApiController::class, 'recipeDetails']);

Route::middleware(['advanced.throttle', 'auth:sanctum'])->group(function () {
    // Auth Actions
    Route::post('auth/logout', [\App\Http\Controllers\API\Auth\AuthApiController::class, 'logoutApi']);
    // AI Recipe Generator
    Route::post('recipe/ai-assist', [\App\Http\Controllers\API\AiRecipeController::class, 'generate']);
    Route::get('recipe/ai-assist/{slug}', [\App\Http\Controllers\API\AiRecipeController::class, 'show']);
    Route::post('recipe/ai-assist/{id}/save', [\App\Http\Controllers\API\AiRecipeController::class, 'storeToKitchen']);
    Route::delete('recipe/ai-assist/{id}', [\App\Http\Controllers\API\AiRecipeController::class, 'destroy']);

    // AI Chat
    Route::get('recipe/ai-chat/start', [\App\Http\Controllers\API\AiChatController::class, 'startChat']);
    Route::post('recipe/ai-chat', [\App\Http\Controllers\API\AiChatController::class, 'sendMessage']);
    Route::get('recipe/ai-chat/history', [\App\Http\Controllers\API\AiChatController::class, 'getHistory']);

    // Profile
    Route::get('profile', [\App\Http\Controllers\API\ProfileApiController::class, 'show']);
    Route::post('profile', [\App\Http\Controllers\API\ProfileApiController::class, 'update']);
    Route::post('change-password', [\App\Http\Controllers\API\ProfileApiController::class, 'updatePassword']);

    // My Kitchen (Saved Recipes)
    Route::get('my-kitchen', [\App\Http\Controllers\API\MyKitchenController::class, 'index']);
    Route::post('my-kitchen', [\App\Http\Controllers\API\MyKitchenController::class, 'store']);
    Route::delete('my-kitchen/favorite/{recipeId}', [\App\Http\Controllers\API\MyKitchenController::class, 'removeFavorite']);
    Route::delete('my-kitchen/ai-recipe/{id}', [\App\Http\Controllers\API\MyKitchenController::class, 'removeAiRecipe']);

});
