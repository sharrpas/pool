<?php

use App\Http\Controllers\Manager\ProfileController;
use App\Http\Controllers\Manager\ReportController;
use App\Http\Controllers\Manager\TableController;
use App\Http\Controllers\Manager\TaskController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|
*/

Route::post('login', [UserController::class, 'login']);
Route::post('signup/role/{role}', [UserController::class, 'signup']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::post('change-password', [UserController::class, 'changePass']);
Route::post('send-code', [UserController::class, 'requestCode']);

Route::get('cities',[SettingController::class,'cities']);

Route::prefix('manager')->middleware(['auth:sanctum', 'role:manager'])->group(function () {

    Route::get('tables', [TableController::class, 'index']);
    Route::get('table/{table}', [TableController::class, 'show']);
    Route::post('table', [TableController::class, 'store']);
    Route::post('table/{table}', [TableController::class, 'update']);
    Route::delete('table/{table}', [TableController::class, 'delete']);

    Route::post('open/table/{table}', [TaskController::class, 'open']);
    Route::post('close/table/{table}', [TaskController::class, 'close']);

    Route::get('tasks/table/{table}', [TaskController::class, 'tasks']);
    Route::post('pay/task/{task}', [TaskController::class, 'pay']);
    Route::post('unpaid/task/{task}', [TaskController::class, 'unpaid']);

    Route::prefix('report')->group(function () {
        Route::post('hours', [ReportController::class, 'HoursPerTables']);
        Route::post('income', [ReportController::class, 'Income']);
    });

    Route::get('profile',[ProfileController::class,'show']);
    Route::post('profile',[ProfileController::class,'update']);

});
