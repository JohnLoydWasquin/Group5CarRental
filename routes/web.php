<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdminCustomerController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminVehicleController;
use App\Http\Controllers\AdminBookingController;

// Navigator
Route::get('/', function () {return view('layouts.pages.home');})->name('home');
// Route::get('/vehicles', function () {return view('layouts.pages.vehicles');})->name('vehicles');
Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles');
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

//Booking & Payment Routes (Only for logged-in users)
Route::middleware(['auth'])->group(function () {
    Route::post('/vehicles', [BookingController::class, 'store'])->name('vehicles.store');
    Route::post('/booking/payment', [BookingController::class, 'submitPayment'])->name('booking.payment');
});

//Admin routing
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
});
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/dashboard', [App\Http\Controllers\StaffController::class, 'index']);
});
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/customers', [AdminCustomerController::class, 'index'])->name('admin.customers');
    Route::get('/admin/customers/search', [AdminCustomerController::class, 'search'])->name('admin.customers.search');
    Route::get('/admin/customers/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');
    Route::delete('/admin/customers/{id}', [AdminCustomerController::class, 'destroy'])->name('admin.customers.destroy');
});
Route::get('/chat/{customer}', [ChatController::class, 'show'])->name('chat.show');
// Admin Car Inventory Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/vehicles', [AdminVehicleController::class, 'index'])->name('admin.vehicles.index');
    Route::post('/vehicles', [AdminVehicleController::class, 'store'])->name('admin.vehicles.store');
    Route::get('/vehicles/{vehicle}/edit', [AdminVehicleController::class, 'edit'])->name('admin.vehicles.edit');
    Route::put('/vehicles/{vehicle}', [AdminVehicleController::class, 'update'])->name('admin.vehicles.update');
    Route::delete('/vehicles/{vehicle}', [AdminVehicleController::class, 'destroy'])->name('admin.vehicles.destroy');
});

// Admin Booking
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('admin.bookings');
    Route::post('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('admin.bookings.confirm');
    Route::post('/bookings/{booking}/ongoing', [AdminBookingController::class, 'markOngoing'])->name('admin.bookings.ongoing');
    Route::post('/bookings/{booking}/completed', [AdminBookingController::class, 'markCompleted'])->name('admin.bookings.completed');
    Route::post('/bookings/{booking}/reject', [AdminBookingController::class, 'reject'])->name('admin.bookings.reject');
    Route::delete('/admin/bookings/{booking}/delete', [AdminBookingController::class, 'destroy'])
    ->name('admin.bookings.destroy');
});





