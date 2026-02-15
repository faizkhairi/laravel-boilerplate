<?php

namespace App\Services;

use Stripe\StripeClient;

/**
 * Opt-in Stripe service. Only active when STRIPE_SECRET_KEY is set.
 *
 * Features:
 * - Create checkout sessions for subscriptions
 * - Handle webhook events (checkout.session.completed, subscription updates, etc.)
 * - Create customers and manage payment methods
 */
class StripeService
{
    protected ?StripeClient $client = null;

    public function __construct()
    {
        $key = config('services.stripe.secret');
        if ($key) {
            $this->client = new StripeClient($key);
        }
    }

    public function isEnabled(): bool
    {
        return $this->client !== null;
    }

    public function client(): ?StripeClient
    {
        return $this->client;
    }

    /**
     * Create a Stripe Checkout session for subscription.
     *
     * @param string $priceId Stripe Price ID
     * @param string $customerEmail Customer email
     * @param string $successUrl URL to redirect after successful payment
     * @param string $cancelUrl URL to redirect if payment is canceled
     * @return \Stripe\Checkout\Session
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createCheckoutSession(
        string $priceId,
        string $customerEmail,
        string $successUrl,
        string $cancelUrl
    ): \Stripe\Checkout\Session {
        if (!$this->isEnabled()) {
            throw new \RuntimeException('Stripe is not configured. Set STRIPE_SECRET_KEY in .env');
        }

        return $this->client->checkout->sessions->create([
            'customer_email' => $customerEmail,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }

    /**
     * Verify webhook signature.
     *
     * @param string $payload Raw request body
     * @param string $signature Stripe-Signature header
     * @return \Stripe\Event
     * @throws \Stripe\Exception\SignatureVerificationException
     */
    public function verifyWebhook(string $payload, string $signature): \Stripe\Event
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        return \Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);
    }
}
