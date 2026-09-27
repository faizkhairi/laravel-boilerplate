<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Jobs\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class SendWelcomeEmailListener
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        /** @var User $user */
        $user = $event->user;

        SendWelcomeEmail::dispatch($user);
    }
}
