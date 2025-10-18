<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class BkashGateway
{
    private string $baseUrl;
    private string $appKey;
    private string $appSecret;
    private string $username;
    private string $password;
    private ?string $callbackUrl;

    public function __construct()
    {
        $config = config('services.bkash', []);

        $this->baseUrl = rtrim((string) ($config['base_url'] ?? ''), '/');
        $this->appKey = (string) ($config['app_key'] ?? '');
        $this->appSecret = (string) ($config['app_secret'] ?? '');
        $this->username = (string) ($config['username'] ?? '');
        $this->password = (string) ($config['password'] ?? '');
        $this->callbackUrl = $config['callback_url'] ?? null;
    }

    public function createCheckout(array $payload): array
    {
        $this->guardConfigured();

        $token = $this->authenticate();

        $defaults = [
            'mode' => '0011',
            'intent' => 'sale',
            'currency' => 'BDT',
        ];

        $requestBody = array_merge($defaults, $payload);

        $response = Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'authorization' => $token,
                'x-app-key' => $this->appKey,
            ])
            ->post('/checkout/payment/create', $requestBody);

        if ($response->failed()) {
            Cache::forget($this->cacheKey());

            Log::error('bKash checkout create failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new RuntimeException($response->json('message') ?? 'Unable to initiate bKash payment.');
        }

        $data = $response->json();

        if (empty($data['paymentID']) || empty($data['bkashURL'] ?? $data['bkashUrl'])) {
            Log::error('bKash checkout create missing paymentID or URL', ['response' => $data]);

            throw new RuntimeException('Invalid response from bKash.');
        }

        return [
            'payment_id' => $data['paymentID'],
            'redirect_url' => $data['bkashURL'] ?? $data['bkashUrl'],
            'raw' => $data,
        ];
    }

    public function executePayment(string $paymentId): array
    {
        $this->guardConfigured();

        $token = $this->authenticate();

        $response = Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'authorization' => $token,
                'x-app-key' => $this->appKey,
            ])
            ->post('/checkout/payment/execute/' . $paymentId, [
                'paymentID' => $paymentId,
            ]);

        if ($response->failed()) {
            Cache::forget($this->cacheKey());

            Log::error('bKash payment execute failed', [
                'payment_id' => $paymentId,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new RuntimeException($response->json('message') ?? 'Failed to confirm bKash payment.');
        }

        return $response->json();
    }

    public function queryPayment(string $paymentId): array
    {
        $this->guardConfigured();

        $token = $this->authenticate();

        $response = Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'authorization' => $token,
                'x-app-key' => $this->appKey,
            ])
            ->get('/checkout/payment/query/' . $paymentId);

        if ($response->failed()) {
            Log::warning('bKash payment query failed', [
                'payment_id' => $paymentId,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new RuntimeException($response->json('message') ?? 'Unable to query bKash payment status.');
        }

        return $response->json();
    }

    public function callbackUrl(?int $paymentId = null): ?string
    {
        if ($this->callbackUrl) {
            return $this->callbackUrl;
        }

        if ($paymentId === null) {
            return null;
        }

        return route('payments.bkash.callback', ['payment' => $paymentId]);
    }

    private function authenticate(): string
    {
        return Cache::remember($this->cacheKey(), now()->addMinutes(55), function (): string {
            $response = Http::baseUrl($this->baseUrl)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'username' => $this->username,
                    'password' => $this->password,
                ])
                ->post('/token/grant', [
                    'app_key' => $this->appKey,
                    'app_secret' => $this->appSecret,
                ]);

            if ($response->failed()) {
                Cache::forget($this->cacheKey());

                Log::error('bKash token request failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                throw new RuntimeException($response->json('message') ?? 'Could not authenticate with bKash.');
            }

            $token = (string) ($response->json('id_token') ?? '');

            if ($token === '') {
                Log::error('bKash token missing in response', ['response' => $response->json()]);

                throw new RuntimeException('Invalid authentication response from bKash.');
            }

            return $token;
        });
    }

    private function guardConfigured(): void
    {
        if ($this->baseUrl === '' || $this->appKey === '' || $this->appSecret === '' || $this->username === '' || $this->password === '') {
            throw new RuntimeException('bKash configuration is missing.');
        }
    }

    private function cacheKey(): string
    {
        return 'bkash_token_' . Str::slug($this->appKey);
    }
}
