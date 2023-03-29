<?php

use App\Http\Controllers\Manager\TableController;
use App\Http\Controllers\Manager\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|
*/

Route::post('login',[UserController::class,'login']);
Route::post('logout',[UserController::class,'logout'])->middleware('auth:sanctum');

Route::prefix('manager')->middleware(['auth:sanctum', 'role:manager'])->group(function () {

    Route::get('tables',[TableController::class,'index']);
    Route::post('table',[TableController::class,'store']);

    Route::post('open/table/{table}',[TaskController::class,'open']);
    Route::post('close/table/{table}',[TaskController::class,'close']);


});


