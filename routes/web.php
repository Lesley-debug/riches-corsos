<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderSuccessController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\PuppyDocumentController;
use App\Http\Controllers\WishlistController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public pages
Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::get('/puppies', [PublicSiteController::class, 'puppyIndex'])->name('puppies.index');
Route::get('/puppies/{puppy:slug}/documents/{document}', [PuppyDocumentController::class, 'publicView'])
    ->name('puppies.documents.show');
Route::get('/puppies/{puppy:slug}', [PublicSiteController::class, 'puppyShow'])->name('puppies.show');
Route::get('/blog', [PublicSiteController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/{blogPost:slug}', [PublicSiteController::class, 'blogShow'])->name('blog.show');
Route::get('/about', [PublicSiteController::class, 'about'])->name('about');
Route::get('/faqs', [PublicSiteController::class, 'faqs'])->name('faqs');
Route::get('/testimonials', [PublicSiteController::class, 'testimonials'])->name('testimonials');
Route::get('/privacy', [PublicSiteController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicSiteController::class, 'terms'])->name('terms');
Route::get('/sitemap.xml', [PublicSiteController::class, 'sitemap'])->name('sitemap');
Route::get('/search/suggestions', [PublicSiteController::class, 'searchSuggestions'])
    ->middleware('throttle:60,1')
    ->name('search.suggestions');

// Contact page + form submission
Route::get('/contact', [PublicSiteController::class, 'contactShow'])->name('contact.show');
Route::post('/contact', [PublicSiteController::class, 'contactStore'])
    ->middleware('throttle:contact-submissions')
    ->name('contact.store');

// Legacy direct order endpoint kept for existing links and integrations.
Route::post('/orders', [OrderController::class, 'store'])
    ->middleware('throttle:order-submissions')
    ->name('orders.store');

// Cart and reservations are intentionally available to guests. Inventory
// checks, transactions, and rate limits still protect the order flow.
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::delete('/cart/{puppy}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/checkout', [CartController::class, 'checkout'])
    ->middleware('throttle:order-submissions')
    ->name('checkout.store');
Route::get('/order/success', [OrderSuccessController::class, '__invoke'])->name('order.success');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])
    ->middleware('throttle:60,1')
    ->name('wishlist.toggle');

// Guest-only auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:registrations');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:password-resets')
        ->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:password-resets')
        ->name('password.update');

    // Google OAuth
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('account.dashboard');
        }

        return Inertia::render('Auth/VerifyEmail');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        $request->user()->claimGuestOrders();

        return redirect()->route('account.dashboard')
            ->with('success', 'Your email address has been verified.');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('account.dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'A new verification link has been sent to your email address.');
    })->middleware('throttle:6,1')->name('verification.send');
});

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

// Private customer data requires both login and verified email ownership.
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/account', [AccountController::class, 'dashboard'])->name('account.dashboard');
    Route::get('/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/notifications', [AccountController::class, 'notifications'])->name('account.notifications');
    Route::post('/account/notifications/read-all', [AccountController::class, 'markAllNotificationsRead'])->name('account.notifications.read-all');
    Route::post('/account/notifications/{id}/read', [AccountController::class, 'markNotificationRead'])->name('account.notifications.read');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
});

// ── Puppy document routes (admin-only, protected by Gate inside controller) ──
Route::middleware('auth')->prefix('admin/puppies/{puppy}/documents')->name('admin.puppies.documents.')->group(function () {
    Route::get('/', [PuppyDocumentController::class, 'index'])->name('index');
    Route::post('/generate', [PuppyDocumentController::class, 'generate'])->name('generate');
    Route::post('/upload', [PuppyDocumentController::class, 'upload'])->name('upload');
    Route::get('/{document}/preview', [PuppyDocumentController::class, 'preview'])->name('preview');
    Route::get('/{document}/download', [PuppyDocumentController::class, 'download'])->name('download');
    Route::post('/{document}/regenerate', [PuppyDocumentController::class, 'regenerate'])->name('regenerate');
    Route::delete('/{document}', [PuppyDocumentController::class, 'destroy'])->name('destroy');
});

// PWA files served via Laravel so they resolve correctly regardless of the
// public_html forwarding setup on Hostinger.
Route::get('/admin-manifest.json', function () {
    return response()->file(public_path('admin-manifest.json'), ['Content-Type' => 'application/manifest+json']);
});
Route::get('/admin/sw.js', function () {
    return response()
        ->file(public_path('admin-sw.js'), ['Content-Type' => 'application/javascript'])
        ->header('Service-Worker-Allowed', '/admin/');
});

// The Filament admin panel is auto-registered by AdminPanelProvider at /admin —
// no routes needed here for that; see app/Providers/Filament/AdminPanelProvider.php
