<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Jobs\SendWelcomeEmail;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendWelcomeEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_the_welcome_mail_to_the_user(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        (new SendWelcomeEmail($user))->handle();

        Mail::assertSent(WelcomeMail::class, function (WelcomeMail $mail) use ($user): bool {
            return $mail->hasTo($user->email) && $mail->user->is($user);
        });
    }

    public function test_it_does_not_throw_when_mail_delivery_fails(): void
    {
        Mail::shouldReceive('to')
            ->once()
            ->andThrow(new \RuntimeException('Connection could not be established with host'));

        $user = User::factory()->create();

        (new SendWelcomeEmail($user))->handle();

        $this->assertTrue(true);
    }
}
