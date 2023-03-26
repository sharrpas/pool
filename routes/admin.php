<?php

use App\Http\Controllers\SuperAdmin\GymController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|
*/

Route::get('gyms',[GymController::class,'index'])->middleware('role:super_admin');
Route::post('add-gym',[GymController::class,'add_gym'])->middleware('role:super_admin');
