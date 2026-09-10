<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedCustomerNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public \App\Models\Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $puppy = $this->order->puppy;

        return (new MailMessage)
            ->subject('Reservation Request Received: ' . ($puppy?->name ?? 'Puppy') . ' — Riches Corsos')
            ->greeting('Hello ' . $this->order->buyer_name . '!')
            ->line('Thank you for placing your reservation request for ' . ($puppy?->name ?? 'your puppy') . '.')
            ->line('We have received your order details and our breeding directors are reviewing your application.')
            ->line('Order ID: #' . $this->order->id)
            ->action('Track Your Order Status', url('/orders'))
            ->line('Our team will reach out to you shortly via phone or email to finalize confirmation and delivery arrangements.')
            ->salutation('Warm regards, The Riches Corsos Team');
    }

    public function toArray(object $notifiable): array
    {
        $puppy = $this->order->puppy;

        return [
            'type' => 'order_placed',
            'title' => 'Reservation Placed: ' . ($puppy?->name ?? 'Puppy'),
            'message' => 'Your reservation request for ' . ($puppy?->name ?? 'your puppy') . ' was placed successfully.',
            'order_id' => $this->order->id,
            'puppy_id' => $this->order->puppy_id,
            'puppy_name' => $puppy?->name ?? 'Puppy',
            'action_url' => '/orders',
            'icon' => 'bag',
        ];
    }
}
