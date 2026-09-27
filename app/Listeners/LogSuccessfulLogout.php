<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\AuditLogger;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $userId = $event->user->getAuthIdentifier();

        if ($userId === null) {
            return;
        }

        AuditLogger::logLogout((int) $userId);
    }
}
