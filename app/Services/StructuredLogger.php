<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Structured logging helper for consistent context across the application.
 *
 * Usage:
 * StructuredLogger::info('User logged in', ['user_id' => $userId]);
 * StructuredLogger::error('Payment failed', ['error' => $e->getMessage()], $e);
 */
class StructuredLogger
{
    /**
     * Log an info message with context.
     */
    public static function info(string $message, array $context = []): void
    {
        Log::info($message, self::enrichContext($context));
    }

    /**
     * Log a warning message with context.
     */
    public static function warning(string $message, array $context = []): void
    {
        Log::warning($message, self::enrichContext($context));
    }

    /**
     * Log an error message with context and optional exception.
     */
    public static function error(string $message, array $context = [], ?\Throwable $exception = null): void
    {
        if ($exception) {
            $context['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        Log::error($message, self::enrichContext($context));
    }

    /**
     * Enrich context with request metadata.
     */
    private static function enrichContext(array $context): array
    {
        return array_merge([
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ], $context);
    }
}
