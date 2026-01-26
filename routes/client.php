<?php

// routes/client.php
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\UserController;
use App\Http\Middleware\CheckSession;
use Illuminate\Support\Facades\Route;

Route::middleware(CheckSession::class)->group(function () {
    Route::resource('orders', OrderController::class);
    Route::post('orders/filter', [OrderController::class, 'filter'])->name('orders.filter');
    Route::get('orders/{order}/show_pdf', [OrderController::class, 'show_pdf'])->name('orders.show_pdf');
    Route::resource('users', UserController::class);
});
