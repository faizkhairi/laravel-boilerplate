<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'event',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    /**
     * Get the user that owns the audit log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an authentication event.
     *
     * @param string $event Event type (LOGIN, LOGOUT, REGISTER, etc.)
     * @param int|null $userId User ID (null for failed attempts)
     * @param array $metadata Additional context
     * @return static
     */
    public static function logAuthEvent(string $event, ?int $userId = null, array $metadata = []): static
    {
        return static::create([
            'user_id' => $userId,
            'event' => $event,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }
}
