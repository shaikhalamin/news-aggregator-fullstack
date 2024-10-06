<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SearchFilterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserFeedController;
use App\Http\Controllers\UserPreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::apiResource('users', UserController::class);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::get('/search-filter', [SearchFilterController::class, 'search']);
    Route::get('/news-categories/{source}', [SearchFilterController::class, 'getSourceCategories']);
    Route::apiResource('user-preferences', UserPreferenceController::class);
    Route::apiResource('user-feeds', UserFeedController::class);
    
});
