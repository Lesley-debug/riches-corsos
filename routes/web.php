<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// Public pages
Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::get('/puppies', [PublicSiteController::class, 'puppyIndex'])->name('puppies.index');
Route::get('/puppies/{puppy:slug}', [PublicSiteController::class, 'puppyShow'])->name('puppies.show');
Route::get('/blog', [PublicSiteController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/{blogPost:slug}', [PublicSiteController::class, 'blogShow'])->name('blog.show');
Route::get('/about', [PublicSiteController::class, 'about'])->name('about');
Route::get('/faqs', [PublicSiteController::class, 'faqs'])->name('faqs');

// Contact page + form submission
Route::get('/contact', [PublicSiteController::class, 'contactShow'])->name('contact.show');
Route::post('/contact', [PublicSiteController::class, 'contactStore'])->name('contact.store');

// "Reserve this puppy" — writes an order request, does not charge any card.
// Guests can order (no ->middleware('auth') here) since requiring an account
// to reserve a puppy would just lose hesitant buyers at the worst moment.
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

// Guest-only auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Customer account area — requires login
Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'dashboard'])->name('account.dashboard');
    Route::get('/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

// The Filament admin panel is auto-registered by AdminPanelProvider at /admin —
// no routes needed here for that; see app/Providers/Filament/AdminPanelProvider.php
