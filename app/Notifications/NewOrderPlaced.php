<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderPlaced extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    // 'database' drives the bell icon inside Filament's admin panel.
    // 'mail' sends the client an email the moment someone reserves a puppy.
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New puppy reservation — '.$this->order->puppy->name)
            ->greeting('New order request')
            ->line($this->order->buyer_name.' has requested '.$this->order->puppy->name.'.')
            ->line('Phone: '.$this->order->buyer_phone)
            ->line('Email: '.$this->order->buyer_email)
            ->action('View in dashboard', url('/admin/orders/'.$this->order->id))
            ->line('No payment has been taken — this is a reservation request only.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'puppy_name' => $this->order->puppy->name,
            'buyer_name' => $this->order->buyer_name,
            'message' => $this->order->buyer_name.' requested '.$this->order->puppy->name,
        ];
    }
}
