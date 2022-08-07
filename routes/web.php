<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('{path?}', function () {
    return view('welcome');
})->where('path', '[a-zA-Z0-9-/]+');


Route::get('/run-artisan', function(){
	Artisan::call('storage:link');
	//Artisan::call('make:mail SendEmail');	
    //Artisan::call('route:cache');
    //Artisan::call('config:clear');
	//Artisan::call('view:clear');
	//Artisan::call('migrate');
    //Artisan::call('make:middleware ForceSSL');
	return "Done";
});

//Route::get('/http-client', [App\Http\Controllers\Client\ClientController::class, 'clientController']);
