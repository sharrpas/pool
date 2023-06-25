<?php

use App\Http\Controllers\Manager\ProductController;
use App\Http\Controllers\Manager\BuffetController;
use App\Http\Controllers\Manager\ProfileController;
use App\Http\Controllers\Manager\ReportController;
use App\Http\Controllers\Manager\TableController;
use App\Http\Controllers\Manager\TaskController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\StoreController;
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

Route::get('cities', [SettingController::class, 'cities']);

Route::prefix('manager')->middleware(['auth:sanctum', 'role:manager'])->group(function () {

    Route::get('tables', [TableController::class, 'index']);
    Route::get('table/{table}', [TableController::class, 'show']);
    Route::post('table', [TableController::class, 'store']);
    Route::post('table/{table}', [TableController::class, 'update']);
    Route::delete('table/{table}', [TableController::class, 'delete']);

    Route::post('open/table/{table}', [TaskController::class, 'open']);
    Route::post('close/table/{table}', [TaskController::class, 'close']);
    Route::post('table/{table}/add-buffet/{buffet}', [TaskController::class, 'add_buffet']);
    Route::post('table/{table}/remove-buffet/{buffet}', [TaskController::class, 'remove_buffet']);

    Route::get('tasks/table/{table}', [TaskController::class, 'tasks']);
    Route::post('pay/task/{task}', [TaskController::class, 'pay']);
    Route::post('unpaid/task/{task}', [TaskController::class, 'unpaid']);

    Route::prefix('report')->group(function () {
        Route::post('hours', [ReportController::class, 'HoursPerTables']);
        Route::post('income', [ReportController::class, 'Income']);
    });

    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile', [ProfileController::class, 'update']);

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{product}', [ProductController::class, 'show']);
        Route::post('/', [ProductController::class, 'store']);
        Route::post('/{product}/update', [ProductController::class, 'update']);
        Route::delete('/{product}/delete', [ProductController::class, 'delete']);
    });

    Route::prefix('buffets')->group(function () {
        Route::get('/', [BuffetController::class, 'index']);
        Route::get('/{buffet}', [BuffetController::class, 'show']);
        Route::post('/', [BuffetController::class, 'store']);
        Route::post('/{buffet}/update', [BuffetController::class, 'update']);
        Route::delete('/{buffet}/delete', [BuffetController::class, 'destroy']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [\App\Http\Controllers\Manager\UserController::class, 'index']);
        Route::post('define-table/{table}', [\App\Http\Controllers\Manager\UserController::class, 'table_for_user']);
    });

});

Route::prefix('user')->group(function () {

    Route::get('dashboard/gyms/city/{city}', [DashboardController::class, 'index']);

    Route::middleware(['auth:sanctum', 'role:user'])->group(function (){

        Route::get('profile', [\App\Http\Controllers\User\ProfileController::class, 'show']);
        Route::post('profile', [\App\Http\Controllers\User\ProfileController::class, 'update']);
        Route::get('dashboard/gym/{gym}',[DashboardController::class,'show']);

        Route::get('store/products/city/{city}',[StoreController::class,'index']);
        Route::get('store/product/{product}',[StoreController::class,'show']);

    });
});


