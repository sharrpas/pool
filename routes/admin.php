<?php

use App\Http\Controllers\SuperAdmin\GymController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {

Route::get('gyms',[GymController::class,'index']);
Route::post('add-gym',[GymController::class,'add_gym']);

});
