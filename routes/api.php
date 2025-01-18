<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [UserController::class, 'store']);
Route::get('login', [UserController::class, 'login'])->name('login');


Route::group(['middleware' => ['auth:api']], function () {
    // Route::get('/users/{id}', [UserController::class, 'edit']);
    Route::post('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::post('users', [UserController::class, 'getUsers']);

    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/role', [RoleController::class, 'store']);
    Route::get('/roles/{id}', [RoleController::class, 'edit']);
    Route::post('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

    Route::post('/logout', [UserController::class, 'logout']);

});