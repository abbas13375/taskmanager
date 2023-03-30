<?php

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

Route::prefix('/auth')->group(function(){
   Route::post('/login', [\App\Http\Controllers\UserController::class, 'actionLogin']);
   Route::post('/signup', [\App\Http\Controllers\UserController::class, 'actionSignup']);
});


Route::middleware('auth:sanctum')->group(function(){
    Route::prefix('/task')->group(function(){
        Route::get('/', [\App\Http\Controllers\TaskController::class, 'actionList']);
        Route::post('/', [\App\Http\Controllers\TaskController::class, 'actionCreate']);

        Route::get('/{id}', [\App\Http\Controllers\TaskController::class, 'actionDetail']);
        Route::patch('/{id}', [\App\Http\Controllers\TaskController::class, 'actionUpdate']);
        Route::delete('/{id}', [\App\Http\Controllers\TaskController::class, 'actionRemove']);
        Route::put('/{id}/complete', [\App\Http\Controllers\TaskController::class, 'actionComplete']);
        Route::put('/{id}/restore', [\App\Http\Controllers\TaskController::class, 'actionRestore']);

    });


});

