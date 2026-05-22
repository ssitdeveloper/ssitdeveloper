<?php

namespace App\Services\PaymentGateways;

interface PaymentGatewayInterface
{
    /**
     * Initialize a payment
     */
    public function initiatePayment(array $data): array;

    /**
     * Verify/capture payment
     */
    public function capturePayment(string $paymentId): array;

    /**
     * Refund a payment
     */
    public function refund(string $transactionId, ?float $amount = null): array;

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $transactionId): string;

    /**
     * Validate webhook signature
     */
    public function validateWebhook(array $data, string $signature): bool;

    /**
     * Handle webhook event
     */
    public function handleWebhook(array $payload): void;

    /**
     * Get gateway name
     */
    public function getName(): string;

    /**
     * Check if gateway is configured
     */
    public function isConfigured(): bool;
}
