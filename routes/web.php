<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('client_create');
});

Route::resource('orders', OrderController::class);

Route::resource('clients', ClientController::class);

