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
    Route::apiResource('tasks', TaskController::class)->except('store');
    Route::post('tasks/store', [TaskController::class, 'store']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'getUser'])->name('user');
});

// public routes
// Route::apiResource('tasks', TaskController::class)->only('index', 'show');
Route::get('tasks/status/{status}', [TaskController::class,'search']);

// auth routes
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
