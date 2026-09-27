<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\StripeService;
use App\Services\StructuredLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $request->validate([
            'priceId' => 'required|string|starts_with:price_',
        ]);

        if (! $this->stripe->isEnabled()) {
            return response()->json([
                'error' => 'Stripe is not configured. Set STRIPE_SECRET_KEY in .env',
            ], 503);
        }

        try {
            $session = $this->stripe->createCheckoutSession(
                $request->priceId,
                auth()->user()->email,
                url('/dashboard?session_id={CHECKOUT_SESSION_ID}'),
                url('/dashboard')
            );

            StructuredLogger::info('Stripe checkout session created', [
                'session_id' => $session->id,
                'price_id' => $request->priceId,
            ]);

            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            StructuredLogger::error('Stripe checkout failed', ['price_id' => $request->priceId], $e);

            return response()->json([
                'error' => 'Failed to create checkout session',
            ], 500);
        }
    }
}
