<?php

use App\Http\Controllers\BoardingHouseController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// route untuk menampilkan halaman utama
Route::get('/', [HomeController::class, 'index'])->name('home');

// route untuk menampilkan category berdasarkan slug
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');
// route untuk menampilkan kota berdasarkan slug
Route::get('/city/{slug}', [CityController::class, 'show'])->name('city.show');

// ========================================================================================
// route untuk menampilkan boarding house berdasarkan slug
Route::get('/boarding-house/{slug}', [BoardingHouseController::class, 'show'])->name('boarding-house.show');
// route untuk menampilkan rooms boarding house bedasarkan detail boarding house yang sudah dipilih (kamar yang tersedia)
Route::get('/boarding-house/{slug}/rooms', [BoardingHouseController::class, 'rooms'])->name('boarding-house.rooms');

// route untuk menampilkan booking boarding house berdasarkan slug
Route::get('/boarding-house/booking/{slug}', [BookingController::class, 'booking'])->name('booking');

// route untuk menampilkan halaman informasi booking, dan menyimpan data informasi booking
Route::get('boarding-house/booking/{slug}/information', [BookingController::class, 'information'])->name('booking.information');
Route::post('boarding-house/booking/{slug}/information/save', [BookingController::class, 'saveInformation'])->name('booking.information.save');

// route untuk checkout dan payment
Route::get('boarding-house/booking/{slug}/checkout', [BookingController::class, 'checkout'])->name('booking.checkout');
Route::post('boarding-house/booking/{slug}/payment', [BookingController::class, 'payment'])->name('booking.payment');

// route untuk booking sukses
Route::get('/booking-success', [BookingController::class, 'success'])->name('booking.success');

// Route untuk menampilkan halaman pencarian boarding house, dan menampilkan hasilnya
Route::get('/find-boarding-house', [BoardingHouseController::class, 'find'])->name('find-boarding-house');
Route::get('/find-results', [BoardingHouseController::class, 'findResults'])->name('find-boarding-house.results');

// route untuk menampilkan halaman cek booking, dan menampilkan hasilnya
Route::get('/check-booking', [BookingController::class, 'check'])->name('check-booking');
Route::post('/check-booking', [BookingController::class, 'show'])->name('check-booking.show');
