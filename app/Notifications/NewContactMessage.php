<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ContactMessage $contactMessage)
    {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New contact form message — '.$this->contactMessage->name)
            ->greeting('New message received')
            ->line($this->contactMessage->message)
            ->action('View in dashboard', url('/admin/contact-messages/'.$this->contactMessage->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->contactMessage->id,
            'from' => $this->contactMessage->name,
            'message' => 'New message from '.$this->contactMessage->name,
        ];
    }
}
