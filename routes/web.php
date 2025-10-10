<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\TicketOrderController as AdminTicketOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\SupportController as AdminSupportController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TicketOrderController;
use App\Http\Controllers\MyTicketController;

// Welcome
Route::get('/welcome', function () { return view('welcome'); });

// Auth
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================
// USER (auth)
// ==========================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('pengguna.dashboard');

    // Shop
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/event/{event}', [ShopController::class, 'show'])->name('shop.show');
    Route::get('/checkout/{event}', [ShopController::class, 'checkout'])->name('shop.checkout');

    // Ticket Orders (buat pesanan)
    Route::post('/ticket-orders', [TicketOrderController::class, 'store'])->name('ticket-orders.store');

    // My Tickets (QR muncul setelah order paid)
    Route::get('/my-tickets', [MyTicketController::class, 'index'])->name('tickets.mine');

    // Profile & others
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/help', [HelpController::class, 'index'])->name('help');
    Route::get('/news', [NewsController::class, 'index'])->name('news');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
});

// ==========================
// ADMIN (auth + admin)
// ==========================
// Catatan: middleware 'admin' sesuai punyamu. Kalau middleware kamu namanya 'is_admin',
// ubah jadi ->middleware(['auth','is_admin'])
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Events (resource)
        Route::resource('/events', AdminEventController::class)->names('events');

        // Ticket Orders Admin
        Route::get('/ticket-orders', [AdminTicketOrderController::class, 'index'])->name('ticket-orders.index');

        // (Saran kuat: ubah ke PATCH supaya RESTful; tapi kalau mau keep GET untuk cepat, biarin)
        Route::match(['patch','post'], '/ticket-orders/{ticketOrder}/status/{status}', [AdminTicketOrderController::class, 'updateStatus'])
            ->whereIn('status', ['paid','rejected','pending'])
            ->name('ticket-orders.updateStatus');

        // Lihat tiket (QR) per order
        Route::get('/ticket-orders/{ticketOrder}/tickets', [AdminTicketOrderController::class, 'tickets'])
            ->name('ticket-orders.tickets');

        // ===== Users (baru) =====
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // ===== Settings (baru) =====
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'store'])->name('settings.store'); // atau update

        // ===== Notifications (baru) =====
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [AdminNotificationController::class, 'readAll'])->name('notifications.readAll');

        // ===== Support / Help & Support (baru) =====
        Route::get('/support', [AdminSupportController::class, 'index'])->name('support.index');
        Route::post('/support', [AdminSupportController::class, 'store'])->name('support.store');
    });
