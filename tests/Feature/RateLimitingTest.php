<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_seventh_registration_attempt_in_a_minute_is_throttled(): void
    {
        $payload = static fn (int $i): array => [
            'name' => 'Test User',
            'email' => "throttle-{$i}@example.com",
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        // Each successful registration signs the user in, and the guest
        // middleware would then redirect before the throttle runs, so sign
        // out between attempts.
        for ($i = 1; $i <= 6; $i++) {
            $response = $this->post('/register', $payload($i));
            $response->assertStatus(302);
            Auth::logout();
        }

        $response = $this->post('/register', $payload(7));

        $response->assertStatus(429);
    }
}
