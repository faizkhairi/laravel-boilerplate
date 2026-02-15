<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogger
{
    /**
     * Log a login event.
     */
    public static function logLogin(?int $userId, bool $success = true, array $metadata = []): void
    {
        AuditLog::logAuthEvent(
            'LOGIN',
            $userId,
            array_merge(['success' => $success], $metadata)
        );
    }

    /**
     * Log a logout event.
     */
    public static function logLogout(int $userId): void
    {
        AuditLog::logAuthEvent('LOGOUT', $userId);
    }

    /**
     * Log a registration event.
     */
    public static function logRegistration(int $userId, array $metadata = []): void
    {
        AuditLog::logAuthEvent('REGISTRATION', $userId, $metadata);
    }

    /**
     * Log a password reset request.
     */
    public static function logPasswordResetRequest(int $userId): void
    {
        AuditLog::logAuthEvent('PASSWORD_RESET_REQUESTED', $userId);
    }

    /**
     * Log a password reset completion.
     */
    public static function logPasswordReset(int $userId): void
    {
        AuditLog::logAuthEvent('PASSWORD_RESET', $userId);
    }

    /**
     * Log an email verification event.
     */
    public static function logEmailVerification(int $userId): void
    {
        AuditLog::logAuthEvent('EMAIL_VERIFIED', $userId);
    }

    /**
     * Log an OAuth login event.
     */
    public static function logOAuthLogin(int $userId, string $provider): void
    {
        AuditLog::logAuthEvent('OAUTH_LOGIN', $userId, ['provider' => $provider]);
    }

    /**
     * Log a failed login attempt.
     */
    public static function logFailedLogin(string $email): void
    {
        AuditLog::logAuthEvent('LOGIN_FAILED', null, ['email' => $email]);
    }
}
