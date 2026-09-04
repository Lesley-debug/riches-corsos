<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessage extends Notification
{
    use Queueable;

    public function __construct(public ContactMessage $contactMessage) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
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
