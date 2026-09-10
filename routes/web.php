<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
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
Route::get('/testimonials', [PublicSiteController::class, 'testimonials'])->name('testimonials');
Route::get('/privacy', [PublicSiteController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicSiteController::class, 'terms'])->name('terms');
Route::get('/sitemap.xml', [PublicSiteController::class, 'sitemap'])->name('sitemap');

// Contact page + form submission
Route::get('/contact', [PublicSiteController::class, 'contactShow'])->name('contact.show');
Route::post('/contact', [PublicSiteController::class, 'contactStore'])->name('contact.store');

// Legacy direct order endpoint kept for existing links and integrations.
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

// Guest-only auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ── Legacy 301 Permanent Redirects ──
Route::permanentRedirect('/shop', '/puppies');
Route::permanentRedirect('/shop/index.php', '/puppies');
Route::permanentRedirect('/shop/product.php', '/puppies');
Route::permanentRedirect('/puppy.php', '/puppies');
Route::permanentRedirect('/about.php', '/about');
Route::permanentRedirect('/about-us.php', '/about');
Route::permanentRedirect('/contact.php', '/contact');
Route::permanentRedirect('/contact-us.php', '/contact');
Route::permanentRedirect('/blog.php', '/blog');
Route::permanentRedirect('/faq.php', '/faqs');
Route::permanentRedirect('/faqs.php', '/faqs');
Route::permanentRedirect('/testimonials.php', '/testimonials');

// Customer account & shopping area — requires login
Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'dashboard'])->name('account.dashboard');
    Route::get('/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{puppy}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout.store');
});

// ── Puppy document routes (admin-only, protected by Gate inside controller) ──
Route::middleware('auth')->prefix('admin/puppies/{puppy}/documents')->name('admin.puppies.documents.')->group(function () {
    Route::get('/',                              [\App\Http\Controllers\PuppyDocumentController::class, 'index'])     ->name('index');
    Route::post('/generate',                     [\App\Http\Controllers\PuppyDocumentController::class, 'generate'])  ->name('generate');
    Route::post('/upload',                       [\App\Http\Controllers\PuppyDocumentController::class, 'upload'])   ->name('upload');
    Route::get('/{document}/preview',            [\App\Http\Controllers\PuppyDocumentController::class, 'preview'])  ->name('preview');
    Route::get('/{document}/download',           [\App\Http\Controllers\PuppyDocumentController::class, 'download']) ->name('download');
    Route::post('/{document}/regenerate',        [\App\Http\Controllers\PuppyDocumentController::class, 'regenerate'])->name('regenerate');
    Route::delete('/{document}',                 [\App\Http\Controllers\PuppyDocumentController::class, 'destroy'])  ->name('destroy');
});

// The Filament admin panel is auto-registered by AdminPanelProvider at /admin —
// no routes needed here for that; see app/Providers/Filament/AdminPanelProvider.php
