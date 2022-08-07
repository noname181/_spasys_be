<?php

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

Route::get('/api_rs', function () {
    return "Wellcome to spasys1 api_rs!";
})->name('api_rs');
Route::middleware('auth')->group(function () {
    Route::prefix('forwarder_info')->name('forwarder_info.')->group(function () {
        Route::post('/create_or_update/{co_no}', [App\Http\Controllers\ForwarderInfo\ForwarderInfoController::class, 'create'])->name('register_forwarder_info');
        Route::post('/{co_no}', [App\Http\Controllers\ForwarderInfo\ForwarderInfoController::class, 'create_with_co_no'])->name('register_forwarder_info');
        Route::get('/{co_no}', [App\Http\Controllers\ForwarderInfo\ForwarderInfoController::class, 'get_all'])->name('get_all_forwarder_info');
    });

    Route::prefix('customs_info')->name('customs_info.')->group(function () {
        Route::post('/create_or_update/{co_no}', [App\Http\Controllers\CustomsInfo\CustomsInfoController::class, 'create'])->name('register_customs_info');
        Route::post('/{co_no}', [App\Http\Controllers\CustomsInfo\CustomsInfoController::class, 'create_with_co_no'])->name('register_customs_info');
        Route::get('/{co_no}', [App\Http\Controllers\CustomsInfo\CustomsInfoController::class, 'get_all'])->name('get_all_customs_info');
    });

});



