<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\UserController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'index')->name('login.index');
    Route::post('/login', 'store')->name('login.store');
    Route::get('/logout', 'destroy')->name('login.destroy');
    Route::get('/add', 'add')->name('login.add');
});

Route::get('/', function () {
    return redirect()->route('login.index');
});

Route::resource('orders', OrderController::class);
Route::get('/orders/{order}/finish', [OrderController::class, 'finish'])->name('orders.finish');
Route::get('/orders/{order}/show_pdf', [OrderController::class, 'show_pdf'])->name('orders.show_pdf');

Route::resource('clients', ClientController::class);

Route::resource('users', UserController::class);

Route::get('/tec_on', [UserController::class, 'tec_on'])->name('tec_on');
Route::put('/tec_on_update', [UserController::class, 'tec_on_update'])->name('tec_on_update');

Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
Route::get('/notes/{order}/create', [NoteController::class, 'create'])->name('notes.create');
Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
Route::get('/notes/{note}/show', [NoteController::class, 'show'])->name('notes.show');
Route::get('/notes/{note}/edit', [NoteController::class, 'edit'])->name('notes.edit');
Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');



