<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\DeliveryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// adding delivery
Route::post('/delivery', [DeliveryController::class, 'add']);
// getting delivery info
Route::get('/delivery/{id}', [DeliveryController::class, 'get'])->where('id', '[0-9]+');
// getting all deliveries by customer's phone number
Route::get('/delivery', [DeliveryController::class, 'getClientPackages']);

