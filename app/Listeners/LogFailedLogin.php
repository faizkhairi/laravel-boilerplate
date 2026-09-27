<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\AuditLogger;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        $email = (string) ($event->credentials['email'] ?? '');

        AuditLogger::logFailedLogin($email);
    }
}
