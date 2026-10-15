<?php

use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\DonorDashboardController;
use App\Http\Controllers\PublicRequestController;
use App\Http\Controllers\RequestTrackingController;
use App\Http\Controllers\VolunteerAuthController;
use App\Http\Controllers\VolunteerReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicRequestController::class, 'home'])->name('home');
Route::get('/emergency-requests', [PublicRequestController::class, 'emergencies'])->name('emergency.requests');
Route::view('/find-donor', 'find-donor')->name('donor.find');

Route::get('/blood-request', fn () => view('blood-request'))->name('blood.request');
Route::post('/blood-request', [BloodRequestController::class, 'store'])->middleware('throttle:5,1')->name('blood.request.store');
Route::get('/blood-request/status/{requestCode}', [RequestTrackingController::class, 'show'])->where('requestCode', '[A-Za-z0-9-]+')->middleware('throttle:30,1')->name('blood.request.track');
Route::post('/blood-request/status/{requestCode}/cancel', [RequestTrackingController::class, 'cancel'])->where('requestCode', '[A-Za-z0-9-]+')->middleware('throttle:5,1')->name('blood.request.cancel');

Route::get('/become-a-donor', fn () => view('donor-register'))->name('donor.register');
Route::post('/become-a-donor', [DonorController::class, 'store'])->middleware('throttle:3,1')->name('donor.store');

Route::get('/otp/{challenge}', [OtpController::class, 'show'])->whereNumber('challenge')->name('otp.show');
Route::post('/otp/{challenge}/resend', [OtpController::class, 'resend'])->whereNumber('challenge')->middleware('throttle:3,1')->name('otp.resend');
Route::post('/otp/{challenge}', [OtpController::class, 'verify'])->whereNumber('challenge')->middleware('throttle:10,1')->name('otp.verify');

Route::get('/donor/dashboard', [DonorDashboardController::class, 'show'])->name('donor.dashboard');
Route::get('/donor/requests', [DonorDashboardController::class, 'requests'])->middleware('throttle:30,1')->name('donor.requests');
Route::post('/donor/requests/{match}/respond', [DonorDashboardController::class, 'respond'])->whereNumber('match')->middleware('throttle:10,1')->name('donor.requests.respond');
Route::post('/donor/availability', [DonorDashboardController::class, 'availability'])->middleware('throttle:10,1')->name('donor.availability');
Route::post('/donor/profile', [DonorDashboardController::class, 'profile'])->middleware('throttle:10,1')->name('donor.profile');
Route::post('/donor/logout', [DonorDashboardController::class, 'logout'])->name('donor.logout');

Route::get('/volunteer/login', [VolunteerAuthController::class, 'show'])->middleware('throttle:10,1')->name('volunteer.login');
Route::post('/volunteer/login', [VolunteerAuthController::class, 'login'])->middleware('throttle:5,1')->name('volunteer.login.submit');
Route::middleware('volunteer')->prefix('volunteer')->name('volunteer.')->group(function () {
    Route::post('/logout', [VolunteerAuthController::class, 'logout'])->name('logout');
    Route::get('/requests', [VolunteerReviewController::class, 'index'])->name('requests');
    Route::post('/requests/{bloodRequest}/review', [VolunteerReviewController::class, 'review'])->whereNumber('bloodRequest')->middleware('throttle:20,1')->name('requests.review');
    Route::post('/requests/{bloodRequest}/match', [VolunteerReviewController::class, 'match'])->whereNumber('bloodRequest')->middleware('throttle:10,1')->name('requests.match');
    Route::get('/requests/{bloodRequest}/contacts', [VolunteerReviewController::class, 'contacts'])->whereNumber('bloodRequest')->name('requests.contacts');
    Route::post('/requests/{bloodRequest}/contacts/log', [VolunteerReviewController::class, 'logContact'])->whereNumber('bloodRequest')->middleware('throttle:20,1')->name('requests.contacts.log');
});

Route::view('/about', 'about')->name('about');
Route::view('/campaigns', 'campaigns')->name('campaigns');
Route::view('/safety', 'safety')->name('safety');
