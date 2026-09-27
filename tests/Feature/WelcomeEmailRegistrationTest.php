<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WelcomeEmailRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_welcome_email_job_is_dispatched_on_registration(): void
    {
        Bus::fake();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'welcome@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::query()->where('email', 'welcome@example.com')->firstOrFail();

        Bus::assertDispatched(SendWelcomeEmail::class, function (SendWelcomeEmail $job) use ($user): bool {
            return $job->user->is($user);
        });
        // Listeners are auto-discovered; a second registration path would
        // send the email twice.
        Bus::assertDispatchedTimes(SendWelcomeEmail::class, 1);
    }

    /**
     * The sync queue driver (the local default) runs SendWelcomeEmail inline
     * during the registration request. If mail delivery threw uncaught, an
     * unreachable SMTP server would turn a successful registration into a
     * 500. The job must swallow that failure so registration still succeeds.
     */
    public function test_registration_succeeds_even_when_mail_delivery_fails(): void
    {
        Mail::shouldReceive('to')
            ->once()
            ->andThrow(new \RuntimeException('Connection could not be established with host'));

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'mail-fail@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
