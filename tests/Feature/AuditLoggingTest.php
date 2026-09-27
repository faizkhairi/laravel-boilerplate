<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuditLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_successful_login_is_audit_logged(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'event' => 'LOGIN',
        ]);
        // Listeners are auto-discovered; registering one twice would log twice.
        $this->assertSame(1, AuditLog::query()->where('event', 'LOGIN')->count());
    }

    public function test_a_failed_login_is_audit_logged_with_the_attempted_email(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $log = AuditLog::query()->where('event', 'LOGIN_FAILED')->first();

        $this->assertNotNull($log);
        $this->assertNull($log->user_id);
        $this->assertSame($user->email, $log->metadata['email']);
        $this->assertArrayNotHasKey('password', $log->metadata);
    }

    public function test_a_logout_is_audit_logged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'event' => 'LOGOUT',
        ]);
    }

    public function test_a_registration_is_audit_logged(): void
    {
        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'new-user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::query()->where('email', 'new-user@example.com')->firstOrFail();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'event' => 'REGISTRATION',
        ]);
    }

    public function test_a_password_reset_link_request_is_audit_logged(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'event' => 'PASSWORD_RESET_REQUESTED',
        ]);
    }

    public function test_a_completed_password_reset_is_audit_logged(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        $notification = Notification::sent($user, ResetPassword::class)->first();

        $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'event' => 'PASSWORD_RESET',
        ]);
    }
}
