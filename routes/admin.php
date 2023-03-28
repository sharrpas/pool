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

Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {

Route::get('gyms',[GymController::class,'index']);
Route::post('add-gym',[GymController::class,'add_gym']);

});
