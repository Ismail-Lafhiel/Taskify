<?php

use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\AuthController;
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

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::resource('tasks', TaskController::class)->except('index', 'show');
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::resource('tasks', TaskController::class)->only('index', 'show');

// auth routes

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
