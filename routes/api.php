<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post("/login",[UserController::class,"login"]);
Route::post("/register",[UserController::class,"register"]);
Route::middleware('auth:sanctum')->group(function () {
    Route::post("/update_user_details",[UserController::class,"update_user_details"]);
    Route::get("/get_user_details",[UserController::class,"get_user_details"]);
    Route::post("/logout",[UserController::class,"logout"]);
    Route::post("/delete_account",[UserController::class,"delete_account"]);
});
