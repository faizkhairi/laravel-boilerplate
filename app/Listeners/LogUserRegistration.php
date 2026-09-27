<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\Registered;

class LogUserRegistration
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        /** @var User $user */
        $user = $event->user;

        AuditLogger::logRegistration($user->getAuthIdentifier());
    }
}
