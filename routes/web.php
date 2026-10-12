<?php

use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\DonorDashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/blood-request', fn () => view('blood-request'))->name('blood.request');
Route::post('/blood-request', [BloodRequestController::class, 'store'])->middleware('throttle:5,1')->name('blood.request.store');

Route::get('/become-a-donor', fn () => view('donor-register'))->name('donor.register');
Route::post('/become-a-donor', [DonorController::class, 'store'])->middleware('throttle:3,1')->name('donor.store');

Route::get('/otp/{challenge}', [OtpController::class, 'show'])->whereNumber('challenge')->name('otp.show');
Route::post('/otp/{challenge}/resend', [OtpController::class, 'resend'])->whereNumber('challenge')->middleware('throttle:3,1')->name('otp.resend');
Route::post('/otp/{challenge}', [OtpController::class, 'verify'])->whereNumber('challenge')->middleware('throttle:10,1')->name('otp.verify');

Route::get('/donor/dashboard', [DonorDashboardController::class, 'show'])->name('donor.dashboard');
Route::post('/donor/availability', [DonorDashboardController::class, 'availability'])->middleware('throttle:10,1')->name('donor.availability');
Route::post('/donor/profile', [DonorDashboardController::class, 'profile'])->middleware('throttle:10,1')->name('donor.profile');
Route::post('/donor/logout', [DonorDashboardController::class, 'logout'])->name('donor.logout');

Route::view('/about', 'about')->name('about');
Route::view('/campaigns', 'campaigns')->name('campaigns');
Route::view('/safety', 'safety')->name('safety');
