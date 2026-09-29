<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Puppy;
use App\Models\User;
use App\Observers\ContactMessageObserver;
use App\Observers\OrderObserver;
use App\Observers\PuppyObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        ContactMessage::observe(ContactMessageObserver::class);
        Puppy::observe(PuppyObserver::class);

        // Gate used by PuppyDocumentController to restrict document routes to admins.
        Gate::define('admin-only', fn (User $user) => $user->isAdmin());

        RateLimiter::for('order-submissions', function (Request $request) {
            return Limit::perMinute(3)->by(
                'orders:'.($request->user()?->id ?: $request->ip())
            );
        });

        RateLimiter::for('contact-submissions', function (Request $request) {
            return Limit::perMinute(5)->by('contact:'.$request->ip());
        });

        RateLimiter::for('registrations', function (Request $request) {
            return Limit::perHour(3)->by('registration:'.$request->ip());
        });

        RateLimiter::for('password-resets', function (Request $request) {
            $emailAndIp = Str::lower((string) $request->input('email')).'|'.$request->ip();

            return [
                Limit::perMinute(5)->by('password-reset-ip:'.$request->ip()),
                Limit::perMinute(2)->by('password-reset-account:'.sha1($emailAndIp)),
            ];
        });
    }
}
