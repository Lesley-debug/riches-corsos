<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WishlistAddedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public \App\Models\Puppy $puppy) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'wishlist_added',
            'title' => 'Saved to Wishlist: ' . $this->puppy->name,
            'message' => 'You added ' . $this->puppy->name . ' to your saved favorites.',
            'puppy_id' => $this->puppy->id,
            'puppy_name' => $this->puppy->name,
            'action_url' => '/puppies/' . $this->puppy->slug,
            'icon' => 'heart',
        ];
    }
}
