<?php
use IlluminateSupportFacadesRoute;
Route::view('/', 'home')->name('home');
Route::view('/blood-request', 'blood-request')->name('blood.request');
Route::view('/become-a-donor', 'donor-register')->name('donor.register');
Route::view('/about', 'about')->name('about');
Route::view('/campaigns', 'campaigns')->name('campaigns');