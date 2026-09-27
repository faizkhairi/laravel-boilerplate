<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;

        AuditLogger::logLogin($user->getAuthIdentifier());
    }
}
