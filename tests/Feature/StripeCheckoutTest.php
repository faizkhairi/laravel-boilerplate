<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_cannot_create_a_checkout_session(): void
    {
        $response = $this->postJson('/stripe/checkout', ['priceId' => 'price_123']);

        $response->assertStatus(401);
    }

    public function test_an_authenticated_user_gets_a_not_configured_error_without_a_stripe_key(): void
    {
        config(['services.stripe.secret' => null]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/stripe/checkout', ['priceId' => 'price_123']);

        $response->assertStatus(503)->assertJsonStructure(['error']);
    }

    public function test_checkout_requires_a_price_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/stripe/checkout', []);

        $response->assertStatus(422)->assertJsonValidationErrors('priceId');
    }
}
