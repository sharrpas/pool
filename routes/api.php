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

    Route::get('tasks/table/{table}',[TaskController::class,'tasks']);
    Route::post('pay/task/{task}',[TaskController::class,'pay']);
    Route::post('unpaid/task/{task}',[TaskController::class,'unpaid']);


});


/*//todo

    composer require cryptommer/smsir

    php artisan vendor:publish --provider Cryptommer\Smsir\SmsirServiceProvider

    SMSIR_API_KEY=
    SMSIR_LINE_NUMBER=

    //use Cryptommer\Smsir\Smsir;
    $send = smsir::Send();
    $parameter = new \Cryptommer\Smsir\Objects\Parameters('CODE', 'srosh');
    $parameters = array($parameter);

    $send->Verify('09184185136', '812390', $parameters);

    return "suc";
*/
