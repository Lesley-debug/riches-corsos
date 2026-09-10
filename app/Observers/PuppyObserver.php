<?php

namespace App\Observers;

use App\Models\Puppy;
use App\Models\User;
use App\Notifications\NewPuppyPostedNotification;
use Illuminate\Support\Facades\Notification;

class PuppyObserver
{
    public function created(Puppy $puppy): void
    {
        if ($puppy->visibility === 'published' && $puppy->status === 'available') {
            $this->notifyRegisteredUsers($puppy);
        }
    }

    public function updated(Puppy $puppy): void
    {
        if ($puppy->wasChanged('visibility') && $puppy->visibility === 'published' && $puppy->status === 'available') {
            $this->notifyRegisteredUsers($puppy);
        }
    }

    protected function notifyRegisteredUsers(Puppy $puppy): void
    {
        $users = User::all();
        if ($users->isNotEmpty()) {
            Notification::send($users, new NewPuppyPostedNotification($puppy));
        }
    }
}
