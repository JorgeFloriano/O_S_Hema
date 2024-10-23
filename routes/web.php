<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckSession;
use App\Http\Controllers\OrderTypeController;
use App\Http\Controllers\NoteTypeController;
use App\Http\Controllers\DefectController;
use App\Http\Controllers\CauseController;
use App\Http\Controllers\SolutionController;

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

Route::middleware(CheckSession::class)->group(function(){
    Route::resource('orders', OrderController::class);
    Route::get('/orders/{order}/finish', [OrderController::class, 'finish'])->name('orders.finish');
    Route::get('/orders/{order}/show_pdf', [OrderController::class, 'show_pdf'])->name('orders.show_pdf');

    Route::resource('clients', ClientController::class);
    
    Route::resource('order_types', OrderTypeController::class);
    Route::get('order_types/{opt}/list', [OrderTypeController::class, 'list'])->name('order_types.list');
    Route::get('order_types/{order_type}/restore', [OrderTypeController::class, 'restore'])->name('order_types.restore');
    Route::get('order_types/{order_type}/desativate', [OrderTypeController::class, 'desativate'])->name('order_types.desativate');

    Route::resource('note_types', NoteTypeController::class);
    Route::get('note_types/{opt}/list', [NoteTypeController::class, 'list'])->name('note_types.list');
    Route::get('note_types/{note_type}/restore', [NoteTypeController::class, 'restore'])->name('note_types.restore');
    Route::get('note_types/{note_type}/desativate', [NoteTypeController::class, 'desativate'])->name('note_types.desativate');

    Route::resource('defects', DefectController::class);
    Route::get('defects/{opt}/list', [DefectController::class, 'list'])->name('defects.list');
    Route::get('defects/{defect}/restore', [DefectController::class, 'restore'])->name('defects.restore');
    Route::get('defects/{defect}/desativate', [DefectController::class, 'desativate'])->name('defects.desativate');

    Route::resource('causes', CauseController::class);
    Route::get('causes/{opt}/list', [CauseController::class, 'list'])->name('causes.list');
    Route::get('causes/{cause}/restore', [CauseController::class, 'restore'])->name('causes.restore');
    Route::get('causes/{cause}/desativate', [CauseController::class, 'desativate'])->name('causes.desativate');

    Route::resource('solutions', SolutionController::class);
    Route::get('solutions/{opt}/list', [SolutionController::class, 'list'])->name('solutions.list');
    Route::get('solutions/{solution}/restore', [SolutionController::class, 'restore'])->name('solutions.restore');
    Route::get('solutions/{solution}/desativate', [SolutionController::class, 'desativate'])->name('solutions.desativate');

    Route::resource('users', UserController::class);

    Route::get('/tec_on', [UserController::class, 'tec_on'])->name('tec_on');
    Route::put('/tec_on_update', [UserController::class, 'tec_on_update'])->name('tec_on_update');

    Route::put('/ord_tec_update', [OrderController::class, 'ord_tec_update'])->name('ord_tec_update');

    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::get('/notes/{order}/create', [NoteController::class, 'create'])->name('notes.create');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::get('/notes/{note}/show', [NoteController::class, 'show'])->name('notes.show');
    Route::get('/notes/{note}/edit', [NoteController::class, 'edit'])->name('notes.edit');
    Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
});


