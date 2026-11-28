<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class PaymenkuService
{
    public function sudahDikonfigurasi(): bool
    {
        return filled(config('services.paymenku.api_key'));
    }

    /**
     * @return array{configured: bool, channels: list<array<string, mixed>>, error: ?string}
     */
    public function daftarKanalAktif(): array
    {
        $apiKey = (string) config('services.paymenku.api_key', '');
        if ($apiKey === '') {
            return ['configured' => false, 'channels' => [], 'error' => null];
        }

        $kunciCache = 'paymenku:channels:'.substr(hash('sha256', $apiKey), 0, 16);

        return Cache::remember($kunciCache, now()->addMinutes(5), function (): array {
            try {
                $respons = $this->klien()->get('/payment-channels');
                $json = $respons->json();

                if ($respons->failed() || ($json['status'] ?? null) === 'error' || ($json['success'] ?? null) === false) {
                    return [
                        'configured' => true,
                        'channels' => [],
                        'error' => (string) ($json['message'] ?? 'Gagal mengambil kanal pembayaran Paymenku.'),
                    ];
                }

                $data = $json['data'] ?? [];

                return [
                    'configured' => true,
                    'channels' => is_array($data) ? array_values($data) : [],
                    'error' => null,
                ];
            } catch (Throwable $e) {
                return [
                    'configured' => true,
                    'channels' => [],
                    'error' => 'Tidak dapat terhubung ke API Paymenku: '.$e->getMessage(),
                ];
            }
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function buatTransaksi(array $payload, string $idempotencyKey): array
    {
        $respons = $this->klien()
            ->withHeaders(['Idempotency-Key' => $idempotencyKey])
            ->post('/transaction/create', $payload);

        $json = $respons->json();

        if ($respons->failed() || ($json['status'] ?? null) === 'error' || ($json['success'] ?? null) === false) {
            $pesan = (string) ($json['message'] ?? 'Gagal membuat transaksi di Paymenku.');
            if (! empty($json['errors']) && is_array($json['errors'])) {
                $detail = collect($json['errors'])->flatten()->first();
                if (is_string($detail) && $detail !== '') {
                    $pesan .= ' ('.$detail.')';
                }
            }

            throw new RuntimeException($pesan);
        }

        return $json;
    }

    /**
     * @return array<string, mixed>
     */
    public function cekStatus(string $orderId): array
    {
        $respons = $this->klien()->get('/check-status/'.urlencode($orderId));
        $json = $respons->json();

        if ($respons->failed() || ($json['status'] ?? null) === 'error' || ($json['success'] ?? null) === false) {
            throw new RuntimeException((string) ($json['message'] ?? 'Gagal memeriksa status transaksi Paymenku.'));
        }

        return $json['data'] ?? [];
    }

    public function verifikasiWebhookSignature(string $rawBody, string $timestamp, string $signature): bool
    {
        $secret = (string) config('services.paymenku.webhook_secret', '');
        if ($secret === '' || $timestamp === '' || $signature === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$rawBody, $secret);

        return hash_equals($expected, $signature);
    }

    private function klien(): PendingRequest
    {
        $apiKey = (string) config('services.paymenku.api_key', '');
        if ($apiKey === '') {
            throw new RuntimeException('PAYMENKU_API_KEY belum dikonfigurasi di file .env.');
        }

        return Http::baseUrl(rtrim((string) config('services.paymenku.base_url', 'https://paymenku.com/api/v1'), '/'))
            ->withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->connectTimeout(8)
            ->timeout(15);
    }
}
