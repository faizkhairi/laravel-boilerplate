<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\Verified;

class LogEmailVerification
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        /** @var User $user */
        $user = $event->user;

        AuditLogger::logEmailVerification($user->getAuthIdentifier());
    }
}
