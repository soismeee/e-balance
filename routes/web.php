<?php

use App\Http\Controllers\FluidBalancesController;
use Illuminate\Support\Facades\Route;


Route::get('/', [FluidBalancesController::class, 'index']);
Route::get('/balance', [FluidBalancesController::class, 'balance']);
Route::post('/save', [FluidBalancesController::class, 'store']);
Route::get('/getBalance', [FluidBalancesController::class, 'getData']);
Route::delete('/delBalance/{id}', [FluidBalancesController::class, 'destroy']);
