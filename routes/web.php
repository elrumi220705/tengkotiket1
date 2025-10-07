<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ContactController;

Route::get('/welcome', function () {
    return view('welcome');
});

// Auth Routes (Login & Register)
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes - Pengguna (Akses setelah Login)
Route::middleware(['auth'])->group(function () {
    // Dashboard Pengguna
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('pengguna.dashboard');

    // Shop & Event Viewing
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/event/{event}', [ShopController::class, 'show'])->name('shop.show'); // Rute Detail Event

    // Rute Navigasi Lain
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/help', [HelpController::class, 'index'])->name('help');
    Route::get('/news', [NewsController::class, 'index'])->name('news');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
});

// Protected Routes - Admin (Akses setelah Login dan Role Check Admin)
Route::middleware(['auth', 'admin'])->group(function () {
    // Dashboard Admin
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Event Management (CRUD)
    Route::resource('admin/events', AdminEventController::class)->names('admin.events');
});
