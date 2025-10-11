<?php

use App\Http\Controllers\Api\Clients\OrderApiController;
use Illuminate\Support\Facades\Route;

Route::resource('/orders', OrderApiController::class);
