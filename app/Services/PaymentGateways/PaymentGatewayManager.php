<?php

namespace App\Services\PaymentGateways;

class PaymentGatewayManager
{
    private array $gateways = [];

    public function __construct()
    {
        $this->registerGateway('stripe', new StripeGateway());
        $this->registerGateway('paypal', new PayPalGateway());
    }

    /**
     * Register a payment gateway
     */
    public function registerGateway(string $name, PaymentGatewayInterface $gateway): void
    {
        $this->gateways[$name] = $gateway;
    }

    /**
     * Get a specific gateway
     */
    public function gateway(string $name): ?PaymentGatewayInterface
    {
        return $this->gateways[$name] ?? null;
    }

    /**
     * Get the default configured gateway
     */
    public function defaultGateway(): ?PaymentGatewayInterface
    {
        $default = config('payment.default_gateway', 'stripe');
        return $this->gateway($default);
    }

    /**
     * Get all available gateways
     */
    public function allGateways(): array
    {
        return $this->gateways;
    }

    /**
     * Get configured gateways only
     */
    public function configuredGateways(): array
    {
        return array_filter($this->gateways, function ($gateway) {
            return $gateway->isConfigured();
        });
    }

    /**
     * Get gateway names
     */
    public function getGatewayNames(): array
    {
        return array_keys($this->gateways);
    }

    /**
     * Initiate payment with specified or default gateway
     */
    public function initiatePayment(array $data, ?string $gatewayName = null): array
    {
        $gateway = $gatewayName
            ? $this->gateway($gatewayName)
            : $this->defaultGateway();

        if (!$gateway) {
            return [
                'success' => false,
                'message' => 'Payment gateway not found',
            ];
        }

        return $gateway->initiatePayment($data);
    }

    /**
     * Capture payment with specified gateway
     */
    public function capturePayment(string $paymentId, ?string $gatewayName = null): array
    {
        $gateway = $gatewayName
            ? $this->gateway($gatewayName)
            : $this->defaultGateway();

        if (!$gateway) {
            return [
                'success' => false,
                'message' => 'Payment gateway not found',
            ];
        }

        return $gateway->capturePayment($paymentId);
    }

    /**
     * Refund payment
     */
    public function refund(string $transactionId, ?string $gatewayName = null, ?float $amount = null): array
    {
        $gateway = $gatewayName
            ? $this->gateway($gatewayName)
            : $this->defaultGateway();

        if (!$gateway) {
            return [
                'success' => false,
                'message' => 'Payment gateway not found',
            ];
        }

        return $gateway->refund($transactionId, $amount);
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $transactionId, ?string $gatewayName = null): string
    {
        $gateway = $gatewayName
            ? $this->gateway($gatewayName)
            : $this->defaultGateway();

        if (!$gateway) {
            return 'unknown';
        }

        return $gateway->getPaymentStatus($transactionId);
    }
}
