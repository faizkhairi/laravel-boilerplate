<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\PasswordResetLinkSent;

class LogPasswordResetLinkRequested
{
    /**
     * Handle the event.
     */
    public function handle(PasswordResetLinkSent $event): void
    {
        /** @var User $user */
        $user = $event->user;

        AuditLogger::logPasswordResetRequest($user->getAuthIdentifier());
    }
}
