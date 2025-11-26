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
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminCustomerController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminVehicleController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffCustomerController;
use App\Http\Controllers\StaffVehicleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\AdminKycController;

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
Route::middleware('auth')->group(function (){
Route::get('/profile', [ProfileController::class, 'index'])->middleware('auth')->name('profile');
Route::post('/profile/upload', [ProfileController::class, 'upload'])->name('profile.upload');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::get('/userBooking', [BookingController::class, 'index'])->name('userBooking');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

//Contact Message
Route::post('/contact/send', [ContactController::class,'sendMessage'])->name('contact_send');

//Booking & Payment Routes (Only for logged-in users)
Route::middleware(['auth'])->group(function () {
    Route::post('/vehicles', [BookingController::class, 'store'])->name('vehicles.store');
    Route::post('/booking/payment', [BookingController::class, 'submitPayment'])->name('booking.payment');
    Route::post('/booking/reserve', [BookingController::class, 'reserve'])->name('booking.reserve');
    Route::get('/my-reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations/{booking}/pay', [ReservationController::class, 'pay'])->name('reservations.pay');
    Route::post('/reservations/{booking}/cancel', [ReservationController::class, 'cancel'])
        ->name('reservations.cancel');
    Route::post('/bookings/{booking}/pay-balance', [BookingController::class, 'payBalance'])
        ->name('reservations.payBalance');
    Route::post('/reservations/{booking}/refund', [ReservationController::class, 'requestRefund'])
        ->name('reservations.refundRequest');
});

//Admin routing
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});

//Admin Customers data
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/customers', [AdminCustomerController::class, 'index'])->name('admin.customers');
    Route::get('/admin/customers/search', [AdminCustomerController::class, 'search'])->name('admin.customers.search');
    Route::get('/admin/customers/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');
    Route::delete('/admin/customers/{id}', [AdminCustomerController::class, 'destroy'])->name('admin.customers.destroy');
});

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
    Route::post('/admin/bookings/{booking}/refund-approve', [AdminBookingController::class, 'approveRefund'])
    ->name('admin.bookings.refund.approve');
    Route::post('/admin/bookings/{booking}/refund-reject', [AdminBookingController::class, 'rejectRefund'])
    ->name('admin.bookings.refund.reject');
});

//Admin staff management
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/staff', [AdminStaffController::class, 'index'])->name('admin.staff.index');
    Route::get('/staff/create', [AdminStaffController::class, 'create'])->name('admin.staff.create');
    Route::post('/staff', [AdminStaffController::class, 'store'])->name('admin.staff.store');
    Route::put('/staff/{id}', [AdminStaffController::class, 'update'])->name('admin.staff.update');
    Route::delete('/staff/{id}', [AdminStaffController::class, 'destroy'])->name('admin.staff.destroy');
});

//Staff routes
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffController::class, 'index'])->name('staff.dashboard');
    Route::get('/staff/customers', [StaffCustomerController::class, 'index'])->name('staff.customers');
    Route::get('/staff/customers/search', [StaffCustomerController::class, 'search'])->name('staff.customers.search');
    Route::get('/staff/customers/{customer}', [StaffCustomerController::class, 'show'])->name('staff.customers.show');
    Route::get('/staff/vehicles',[StaffVehicleController::class, 'index'])->name('staff.vehicles.index');
});

//Staff/Admin Chatting system
Route::middleware(['auth'])->group(function () {
    Route::get('/chat/{userId}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/send-audio', [ChatController::class, 'sendAudio'])->name('chat.send.audio');
    Route::post('/chat/send-file', [ChatController::class, 'sendFile'])->name('chat.send.file');
    // Route::delete('/chat/delete/{id}', [ChatController::class, 'delete'])->name('chat.delete');
});

//Staff/Admin Generating report
Route::middleware(['auth'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});

// Verify Users
Route::middleware('auth')->group(function () {
    Route::get('/kyc/verify', [KycController::class, 'create'])->name('kyc.form');
    Route::post('/kyc/verify', [KycController::class, 'store'])->name('kyc.store');
});

// Admin – Account Verification (KYC)
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/verifications', [AdminKycController::class, 'index'])->name('admin.kyc.index');
    Route::get('/verifications/{submission}', [AdminKycController::class, 'show'])->name('admin.kyc.show');
    Route::post('/verifications/{submission}/approve', [AdminKycController::class, 'approve'])->name('admin.kyc.approve');
    Route::post('/verifications/{submission}/reject', [AdminKycController::class, 'reject'])->name('admin.kyc.reject');
});
