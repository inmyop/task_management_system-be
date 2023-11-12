<?php

use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Test;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::get('/success', [Test::class, 'successExample']);
    Route::post('/success/created', [Test::class, 'successCreatedExample']);

    Route::post('/error', [Test::class, 'errorExample']);
    Route::get('/404', [Test::class, 'notFoundExample']);

    Route::get('/unauthorized', [Test::class, 'unauthorizedExample']);
    Route::get('/unauthenticated', [Test::class, 'unauthenticatedExample']);

    Route::get('/forbidden', [Test::class, 'forbiddenExample']);
    Route::get('/error/internal', [Test::class, 'serverErrorExample']);

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'user'], function () {
        Route::get('profile', [UserController::class, 'getProfile']);
        Route::post('profile/update', [UserController::class, 'updateProfile']);
    });
    
    Route::post('/token', [AuthController::class, 'checkToken']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'task'], function () {
        Route::post('add', [TaskController::class, 'addTask']);
        Route::get('get', [TaskController::class, 'getTasks']);
        Route::get('detail/{task_code}', [TaskController::class, 'getDetail']);
        Route::post('update', [TaskController::class, 'updateTask']);
        Route::delete('delete/{task_code}', [TaskController::class, 'deleteTask']);
    });
});