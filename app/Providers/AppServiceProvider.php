<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Puppy;
use App\Models\User;
use App\Observers\ContactMessageObserver;
use App\Observers\OrderObserver;
use App\Observers\PuppyObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
    }
}
