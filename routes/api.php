<?php

use App\Http\Controllers\Api\MidtransController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// buat route untuk menerima callback dari midtrans setelah pembayaran selesai
Route::post('/midtrans-callback', [MidtransController::class, 'callback']);
