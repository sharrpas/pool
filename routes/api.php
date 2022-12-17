<?php

use App\Http\Controllers\TableController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|
*/


Route::get('tables',[TableController::class,'index']);
Route::post('table',[TableController::class,'store']);

Route::post('open/table/{table}',[TaskController::class,'open']);
Route::post('close/table/{table}',[TaskController::class,'close']);
