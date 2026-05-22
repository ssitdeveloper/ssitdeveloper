<?php

namespace App\Services\PaymentGateways;

use App\Models\Payment;
use Illuminate\Support\Str;

class PayPalGateway implements PaymentGatewayInterface
{
    private string $clientId;
    private string $clientSecret;
    private string $mode;
    private string $apiUrl;

    public function __construct()
    {
        $this->clientId = config('payment.paypal.client_id', '');
        $this->clientSecret = config('payment.paypal.client_secret', '');
        $this->mode = config('payment.paypal.mode', 'sandbox');
        $this->apiUrl = $this->mode === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    /**
     * Initialize a payment
     */
    public function initiatePayment(array $data): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'PayPal is not configured. Please add your credentials.',
                'approval_url' => null,
            ];
        }

        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to get PayPal access token',
                ];
            }

            $response = $this->makeRequest('POST', '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => $data['currency'] ?? 'USD',
                            'value' => number_format($data['amount'], 2, '.', ''),
                        ],
                        'description' => $data['description'] ?? 'Subscription Payment',
                    ],
                ],
                'application_context' => [
                    'return_url' => $data['return_url'] ?? route('student.subscription'),
                    'cancel_url' => $data['cancel_url'] ?? route('student.subscription'),
                ],
            ], $accessToken);

            if (isset($response['id'])) {
                $approvalUrl = collect($response['links'])->firstWhere('rel', 'approve')['href'] ?? null;

                return [
                    'success' => true,
                    'order_id' => $response['id'],
                    'approval_url' => $approvalUrl,
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to create PayPal order',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to initiate payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Capture/approve a payment
     */
    public function capturePayment(string $orderId): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'PayPal is not configured',
            ];
        }

        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to get PayPal access token',
                ];
            }

            $response = $this->makeRequest('POST', "/v2/checkout/orders/$orderId/capture", [], $accessToken);

            if (isset($response['id']) && $response['status'] === 'COMPLETED') {
                $transactionId = $response['purchase_units'][0]['payments']['captures'][0]['id'] ?? $response['id'];

                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'order_id' => $response['id'],
                    'status' => 'completed',
                ];
            }

            return [
                'success' => false,
                'status' => $response['status'] ?? 'unknown',
                'message' => $response['message'] ?? 'Payment not completed',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to capture payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Refund a payment
     */
    public function refund(string $captureId, ?float $amount = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'PayPal is not configured',
            ];
        }

        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to get PayPal access token',
                ];
            }

            $body = [];
            if ($amount) {
                $body['amount'] = [
                    'currency_code' => 'USD',
                    'value' => number_format($amount, 2, '.', ''),
                ];
            }

            $response = $this->makeRequest(
                'POST',
                "/v2/payments/captures/$captureId/refund",
                $body,
                $accessToken
            );

            if (isset($response['status'])) {
                return [
                    'success' => true,
                    'refund_id' => $response['id'],
                    'status' => strtolower($response['status']),
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to refund',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to refund payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $orderId): string
    {
        if (!$this->isConfigured()) {
            return 'unknown';
        }

        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return 'unknown';
            }

            $response = $this->makeRequest('GET', "/v2/checkout/orders/$orderId", [], $accessToken);

            return match ($response['status'] ?? 'unknown') {
                'CREATED' => 'pending',
                'APPROVED' => 'approved',
                'VOIDED' => 'cancelled',
                'COMPLETED' => 'completed',
                default => strtolower($response['status'] ?? 'unknown'),
            };
        } catch (\Exception $e) {
            return 'unknown';
        }
    }

    /**
     * Validate webhook signature
     */
    public function validateWebhook(array $data, string $signature): bool
    {
        if (!$this->clientId || !$this->clientSecret) {
            return false;
        }

        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return false;
            }

            $webhookId = config('payment.paypal.webhook_id', '');
            if (!$webhookId) {
                return false;
            }

            $body = [
                'transmission_id' => $data['transmission_id'] ?? '',
                'transmission_time' => $data['transmission_time'] ?? '',
                'cert_url' => $data['cert_url'] ?? '',
                'auth_algo' => $data['auth_algo'] ?? '',
                'transmission_sig' => $data['transmission_sig'] ?? '',
                'webhook_id' => $webhookId,
                'webhook_event' => $data,
            ];

            $response = $this->makeRequest('POST', '/v1/notifications/verify-webhook-signature', $body, $accessToken);

            return ($response['verification_status'] ?? '') === 'SUCCESS';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Handle webhook event
     */
    public function handleWebhook(array $payload): void
    {
        $event = $payload['event_type'] ?? null;

        match ($event) {
            'PAYMENT.CAPTURE.COMPLETED' => $this->handlePaymentCompleted($payload['resource']),
            'PAYMENT.CAPTURE.DENIED' => $this->handlePaymentDenied($payload['resource']),
            'PAYMENT.CAPTURE.REFUNDED' => $this->handleRefunded($payload['resource']),
            default => null,
        };
    }

    private function handlePaymentCompleted(array $resource): void
    {
        $payment = Payment::where('transaction_id', $resource['id'])->first();

        if ($payment) {
            $payment->update([
                'status' => 'completed',
            ]);

            if ($payment->subscription) {
                $payment->subscription->update([
                    'status' => 'active',
                ]);
            }
        }
    }

    private function handlePaymentDenied(array $resource): void
    {
        $payment = Payment::where('transaction_id', $resource['id'])->first();

        if ($payment) {
            $payment->update([
                'status' => 'failed',
            ]);
        }
    }

    private function handleRefunded(array $resource): void
    {
        $payment = Payment::where('transaction_id', $resource['supplementary_data']['related_ids']['order_id'] ?? null)->first();

        if ($payment) {
            $payment->update([
                'status' => 'refunded',
            ]);
        }
    }

    private function getAccessToken(): ?string
    {
        try {
            $response = $this->makeRequestRaw('POST', '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

            return $response['access_token'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function makeRequest(string $method, string $endpoint, array $body = [], string $accessToken = ''): array
    {
        $url = $this->apiUrl . $endpoint;

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POSTFIELDS => $body ? json_encode($body) : null,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }

    private function makeRequestRaw(string $method, string $endpoint, array $body = []): array
    {
        $url = $this->apiUrl . $endpoint;
        $auth = base64_encode($this->clientId . ':' . $this->clientSecret);

        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ' . $auth,
        ];

        $postBody = http_build_query($body);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POSTFIELDS => $postBody,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }

    public function getName(): string
    {
        return 'PayPal';
    }

    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }
}
