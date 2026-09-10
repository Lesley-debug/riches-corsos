<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Riches Corsos! Start shopping now')
            ->greeting('Welcome to Riches Corsos, ' . $notifiable->name . '!')
            ->line('We are delighted to welcome you to our Cane Corso family.')
            ->line('Explore our champion-line Cane Corso puppies, follow upcoming litters, save favorites to your wishlist, and track your reservations directly from your personal account dashboard.')
            ->action('Start Shopping — Browse Available Puppies', url('/puppies'))
            ->line('If you have any questions or need guidance picking the right companion, our dedicated breeding team is always here to assist.')
            ->salutation('Warm regards, The Riches Corsos Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'welcome',
            'title' => 'Welcome to Riches Corsos!',
            'message' => 'Welcome to the family! Start browsing our available champion-line Cane Corso puppies.',
            'action_url' => '/puppies',
            'icon' => 'sparkles',
        ];
    }
}
