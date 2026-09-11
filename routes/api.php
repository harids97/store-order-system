<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;



Route::post('/orders', [OrderController::class, 'store']);
Route::get('/customers/orders', [OrderController::class, 'history']);
Route::get('/products/low-stock', [OrderController::class, 'lowStock']);
Route::get('/products', [OrderController::class, 'products']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
