<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/users", [RegisterController::class, 'register']);
Route::post("/login", [RegisterController::class, 'login']);
Route::post("/reset", [RegisterController::class, 'resetPassword']);
Route::post("/new_password", [RegisterController::class, 'newPassword']);
Route::middleware('auth:api')->put("/users/{id}", [RegisterController::class, 'update']);
Route::get("/users", [RegisterController::class, 'show']);
Route::middleware('auth:api')->get("/users/{id}", [RegisterController::class, 'showId']);
Route::middleware('auth:api')->delete("/users/{id}", [RegisterController::class, 'delete']);



