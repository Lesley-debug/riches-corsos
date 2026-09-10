<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPuppyPostedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public \App\Models\Puppy $puppy) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Corso Alert: ' . $this->puppy->name . ' is now available at Riches Corsos!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A magnificent new champion-line Cane Corso puppy has just been published to our available roster!')
            ->line('Name: ' . $this->puppy->name . ' (' . ucfirst($this->puppy->sex) . ', ' . ($this->puppy->color ?? 'Standard') . ')')
            ->line('Price: $' . number_format((float) $this->puppy->price, 2))
            ->action('Meet ' . $this->puppy->name, url('/puppies/' . $this->puppy->slug))
            ->line('Our litters reserve swiftly. If you are ready to welcome ' . $this->puppy->name . ' to your family, review their profile and submit your reservation request.')
            ->salutation('Warm regards, The Riches Corsos Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_puppy',
            'title' => 'New Corso Available: ' . $this->puppy->name,
            'message' => $this->puppy->name . ' (' . ucfirst($this->puppy->sex) . ', ' . ($this->puppy->color ?? 'Standard') . ') is now available.',
            'puppy_id' => $this->puppy->id,
            'puppy_name' => $this->puppy->name,
            'action_url' => '/puppies/' . $this->puppy->slug,
            'icon' => 'paw',
        ];
    }
}
