<?php

namespace App\Observers;

use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\NewContactMessage;
use Illuminate\Support\Facades\Notification;

class ContactMessageObserver
{
    public function created(ContactMessage $contactMessage): void
    {
        $admins = User::where('role', 'admin')->get();

        Notification::send($admins, new NewContactMessage($contactMessage));
    }
}
