<?php

use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\OtpController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/blood-request', fn () => view('blood-request'))->name('blood.request');
Route::post('/blood-request', [BloodRequestController::class, 'store'])->name('blood.request.store');

Route::get('/become-a-donor', fn () => view('donor-register'))->name('donor.register');
Route::post('/become-a-donor', [DonorController::class, 'store'])->name('donor.store');

Route::get('/otp/{challenge}', [OtpController::class, 'show'])->name('otp.show');
Route::post('/otp/{challenge}/resend', [OtpController::class, 'resend'])->name('otp.resend');
Route::post('/otp/{challenge}', [OtpController::class, 'verify'])->name('otp.verify');

Route::view('/about', 'about')->name('about');
Route::view('/campaigns', 'campaigns')->name('campaigns');
