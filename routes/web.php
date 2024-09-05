<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('orders_list');
});

Route::resource('orders', OrderController::class);

Route::resource('clients', ClientController::class);

Route::resource('notes', NoteController::class);

