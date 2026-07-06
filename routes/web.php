<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google OAuth Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Email Verification Routes
Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

use App\Http\Controllers\ProductController;
use App\Http\Controllers\TillController;

// Marketing Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');

// Invoices (Checks auth inside Controller)
Route::resource('invoices', InvoiceController::class);

// POS Till Routes (Accessible by guests)
Route::get('/till', [TillController::class, 'index'])->name('till.index');
Route::post('/till', [TillController::class, 'store'])->name('till.store');

// Profile & Product Management Settings
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/imgbb/upload', [ProductController::class, 'uploadImgbb'])->name('imgbb.upload');
    Route::resource('products', ProductController::class);
});
