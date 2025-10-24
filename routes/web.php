<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ContactController;

// Navigator
Route::get('/', function () {return view('layouts.pages.home');})->name('home');
Route::get('/vehicles', function () {return view('layouts.pages.vehicles');})->name('vehicles');
Route::get('/about', function () {return view('layouts.pages.about');})->name('about');
Route::get('/contact', function () {return view('layouts.pages.contact');})->name('contact');

// Login Page
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class,'login'])->name('login');
Route::post('/logout', [LoginController::class,'logout'])->name('logout');

// Forgot Password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('forgot_phone');
Route::post('/forgot-password/send', [ForgotPasswordController::class, 'sendOtp'])->name('forgot_sendOtp');
Route::get('/verify-otp', [ForgotPasswordController::class, 'showVerifyForm'])->name('verify_otp');
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('verify_otp');
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetPassword'])->name('reset_password');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset_password');

//Users Profile
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::get('/userBooking', [BookingController::class, 'index'])->name('userBooking');
Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//Contact Message
Route::post('/contact/send', [ContactController::class,'sendMessage'])->name('contact_send');
