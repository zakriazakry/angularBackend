<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolesController;
use App\Http\Middleware\Cors;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::controller(AuthController::class)
    ->prefix('auth')
    ->middleware(Cors::class)
    ->group(function () {
        Route::post('login', 'login');
        Route::post('signup', 'signup');
    });

// Role management routes
Route::middleware(['auth:sanctum', Cors::class])
    ->controller(RolesController::class)
    ->prefix('roles')
    ->group(function () {
        Route::get('getRole', 'getAllRole');
        Route::get('getRole/{user_id}', 'getUserRole');
        Route::post('setUserRole/{user_id}', 'setUserRole');
        Route::post('addUserRole/{user_id}', 'addUserRole');
        Route::post('removeUserRole/{user_id}', 'removeUserRole');
    });
