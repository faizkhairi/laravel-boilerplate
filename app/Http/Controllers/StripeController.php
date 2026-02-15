<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use App\Services\StructuredLogger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StripeController extends Controller
{
    public function __construct(
        private StripeService $stripe
    ) {}

    /**
     * Create a Stripe Checkout session.
     *
     * POST /stripe/checkout
     * Body: { "priceId": "price_xxx" }
     * Returns: { "url": "https://checkout.stripe.com/..." }
     */
    public function checkout(Request $request): JsonResponse
    {
        if (!$this->stripe->isEnabled()) {
            return response()->json([
                'error' => 'Stripe is not configured. Set STRIPE_SECRET_KEY in .env'
            ], 503);
        }

        $request->validate([
            'priceId' => 'required|string|starts_with:price_',
        ]);

        try {
            $session = $this->stripe->createCheckoutSession(
                $request->priceId,
                auth()->user()->email,
                url('/dashboard?session_id={CHECKOUT_SESSION_ID}'),
                url('/subscription')
            );

            StructuredLogger::info('Stripe checkout session created', [
                'session_id' => $session->id,
                'price_id' => $request->priceId,
            ]);

            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            StructuredLogger::error('Stripe checkout failed', ['price_id' => $request->priceId], $e);

            return response()->json([
                'error' => 'Failed to create checkout session'
            ], 500);
        }
    }

    /**
     * Handle Stripe webhook events.
     *
     * POST /stripe/webhook
     * Events: checkout.session.completed, customer.subscription.updated, etc.
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $payload = $request->getContent();
            $signature = $request->header('Stripe-Signature');

            $event = $this->stripe->verifyWebhook($payload, $signature);

            StructuredLogger::info('Stripe webhook received', [
                'event_type' => $event->type,
                'event_id' => $event->id,
            ]);

            // Handle different event types
            match ($event->type) {
                'checkout.session.completed' => $this->handleCheckoutComplete($event->data->object),
                'customer.subscription.updated' => $this->handleSubscriptionUpdate($event->data->object),
                'customer.subscription.deleted' => $this->handleSubscriptionDelete($event->data->object),
                'invoice.payment_succeeded' => $this->handlePaymentSucceeded($event->data->object),
                'invoice.payment_failed' => $this->handlePaymentFailed($event->data->object),
                default => StructuredLogger::info('Unhandled Stripe event', ['type' => $event->type]),
            };

            return response()->json(['received' => true]);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            StructuredLogger::error('Stripe webhook signature verification failed', [], $e);

            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            StructuredLogger::error('Stripe webhook processing failed', [], $e);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    private function handleCheckoutComplete($session): void
    {
        StructuredLogger::info('Checkout completed', [
            'session_id' => $session->id,
            'customer_id' => $session->customer ?? null,
        ]);

        // TODO: Update user subscription status in database
    }

    private function handleSubscriptionUpdate($subscription): void
    {
        StructuredLogger::info('Subscription updated', [
            'subscription_id' => $subscription->id,
            'status' => $subscription->status,
        ]);

        // TODO: Update subscription in database
    }

    private function handleSubscriptionDelete($subscription): void
    {
        StructuredLogger::warning('Subscription deleted', [
            'subscription_id' => $subscription->id,
        ]);

        // TODO: Cancel subscription in database
    }

    private function handlePaymentSucceeded($invoice): void
    {
        StructuredLogger::info('Payment succeeded', [
            'invoice_id' => $invoice->id,
            'amount' => $invoice->amount_paid,
        ]);

        // TODO: Record payment in database
    }

    private function handlePaymentFailed($invoice): void
    {
        StructuredLogger::warning('Payment failed', [
            'invoice_id' => $invoice->id,
            'attempt_count' => $invoice->attempt_count ?? null,
        ]);

        // TODO: Notify user of failed payment
    }
}
