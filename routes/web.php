<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\TicketOrderController as AdminTicketOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TicketOrderController;

// Welcome
Route::get('/welcome', function () { return view('welcome'); });

// Auth
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// USER (auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('pengguna.dashboard');

    // Shop
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/event/{event}', [ShopController::class, 'show'])->name('shop.show');
    Route::get('/checkout/{event}', [ShopController::class, 'checkout'])->name('shop.checkout');

    // Ticket Orders (buat pesanan)
    Route::post('/ticket-orders', [TicketOrderController::class, 'store'])->name('ticket-orders.store');

    // Profile & others
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/help', [HelpController::class, 'index'])->name('help');
    Route::get('/news', [NewsController::class, 'index'])->name('news');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
});

// ADMIN (auth + admin)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('admin/events', AdminEventController::class)->names('admin.events');

    // Ticket Orders Admin
    Route::get('admin/ticket-orders', [AdminTicketOrderController::class, 'index'])->name('admin.ticket-orders.index');
    Route::get('admin/ticket-orders/{ticketOrder}/{status}', [AdminTicketOrderController::class, 'updateStatus'])
        ->whereIn('status', ['paid','rejected','pending'])
        ->name('admin.ticket-orders.updateStatus');
});
