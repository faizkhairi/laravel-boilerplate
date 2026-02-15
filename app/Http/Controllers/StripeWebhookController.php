<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Handles Stripe webhooks (opt-in). Only processes when STRIPE_WEBHOOK_SECRET is set.
 */
class StripeWebhookController extends Controller
{
    public function __construct(
        protected StripeService $stripe
    ) {}

    public function __invoke(Request $request): Response
    {
        $secret = config('services.stripe.webhook_secret');
        if (! $secret) {
            return response('Webhook not configured', 400);
        }

        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig, $secret);
        } catch (\Exception $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                // Handle subscription lifecycle (e.g. update local subscription status)
                Log::info('Stripe subscription event', ['type' => $event->type, 'id' => $event->id]);
                break;
            case 'invoice.paid':
            case 'invoice.payment_failed':
                Log::info('Stripe invoice event', ['type' => $event->type, 'id' => $event->id]);
                break;
            default:
                Log::info('Stripe webhook received', ['type' => $event->type]);
        }

        return response('OK', 200);
    }
}
