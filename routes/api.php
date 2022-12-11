<?php

use App\Http\Controllers\TableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|
*/


Route::get('tables',[TableController::class,'index']);
Route::post('table',[TableController::class,'store']);
