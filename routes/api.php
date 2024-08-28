<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\Cors;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('login','login');
    Route::post('signup','signup');

})->middleware(Cors::class);
