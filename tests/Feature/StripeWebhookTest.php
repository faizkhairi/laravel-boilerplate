<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    public function test_webhook_is_rejected_when_not_configured(): void
    {
        config(['services.stripe.webhook_secret' => null]);

        $this->postJson('/stripe/webhook', [])->assertStatus(400);
    }

    public function test_webhook_without_a_signature_header_is_rejected(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test']);

        $this->postJson('/stripe/webhook', ['type' => 'invoice.paid'])
            ->assertStatus(400)
            ->assertSee('Invalid signature');
    }

    public function test_webhook_with_a_forged_signature_is_rejected(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test']);

        $this->postJson('/stripe/webhook', ['type' => 'invoice.paid'], [
            'Stripe-Signature' => 't=1,v1=forged',
        ])->assertStatus(400);
    }
}
