<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Observers\ContactMessageObserver;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        ContactMessage::observe(ContactMessageObserver::class);
    }
}
