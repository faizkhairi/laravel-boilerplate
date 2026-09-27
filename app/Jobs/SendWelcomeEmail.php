<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user
    ) {}

    /**
     * Execute the job.
     *
     * The mailer is unreachable in some environments (e.g. no SMTP server yet
     * configured). Registration must never fail because the welcome email
     * could not be sent, so a delivery failure is caught and logged rather
     * than allowed to bubble up (which, on the sync queue, would turn into a
     * failed registration request).
     */
    public function handle(): void
    {
        try {
            Mail::to($this->user->email)->send(new WelcomeMail($this->user));
        } catch (\Throwable $e) {
            Log::warning('Failed to send welcome email', [
                'user_id' => $this->user->id,
            ]);
        }
    }
}
