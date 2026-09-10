<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderPlaced;
use Illuminate\Support\Facades\Notification;

class OrderObserver
{
    public function created(Order $order): void
    {
        $admins = User::where('role', User::ROLE_ADMIN)->get();

        Notification::send($admins, new NewOrderPlaced($order));

        if ($order->user) {
            $order->user->notify(new \App\Notifications\OrderPlacedCustomerNotification($order));
        } elseif ($order->buyer_email) {
            Notification::route('mail', $order->buyer_email)
                ->notify(new \App\Notifications\OrderPlacedCustomerNotification($order));
        }
    }
}
