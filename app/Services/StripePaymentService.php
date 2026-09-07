<?php

namespace App\Services;

use App\Models\Investigation;
use App\Models\Lab;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    protected string $secretKey;
    protected string $publishableKey;

    public function __construct(?Lab $lab = null)
    {
        $this->secretKey = $lab?->stripe_secret ?: (config('services.stripe.secret') ?: env('STRIPE_SECRET', ''));
        $this->publishableKey = $lab?->stripe_key ?: (config('services.stripe.key') ?: env('STRIPE_KEY', ''));

        if (!empty($this->secretKey)) {
            Stripe::setApiKey($this->secretKey);
        }
    }

    public function isConfigured(): bool
    {
        return !empty($this->secretKey);
    }

    public function getPublishableKey(): string
    {
        return $this->publishableKey;
    }

    /**
     * Create a Stripe Hosted Checkout Session
     */
    public function createCheckoutSession(Investigation $investigation, float $amountToPay): ?Session
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $patient = $investigation->patient;
            $lab = $investigation->lab;
            $currency = 'inr'; // Standard INR or fallback to usd

            $session = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => $patient?->email ?: null,
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => 'Diagnostic Investigation: INV-' . $investigation->id,
                            'description' => 'Patient: ' . ($patient?->name ?? 'N/A') . ' | ' . ($lab?->name ?? 'Diagnostic Lab'),
                        ],
                        'unit_amount' => (int) round($amountToPay * 100), // In subunits (paise / cents)
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}&inv_id=' . $investigation->id,
                'cancel_url' => route('payment.stripe.cancel') . '?inv_id=' . $investigation->id,
                'metadata' => [
                    'investigation_id' => $investigation->id,
                    'lab_id' => $investigation->lab_id,
                    'patient_id' => $investigation->patient_id,
                    'amount' => $amountToPay,
                ],
            ]);

            return $session;
        } catch (\Throwable $e) {
            Log::error('Stripe Checkout Session Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve Session
     */
    public function retrieveSession(string $sessionId): ?Session
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            return Session::retrieve($sessionId);
        } catch (\Throwable $e) {
            Log::error('Stripe Retrieve Session Error: ' . $e->getMessage());
            return null;
        }
    }
}
