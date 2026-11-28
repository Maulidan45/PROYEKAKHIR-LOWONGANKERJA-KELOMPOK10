<?php

namespace App\Http\Controllers;

use App\Models\TransaksiAd;
use App\Services\PaymenkuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymenkuWebhookController extends Controller
{
    public function handle(Request $permintaan, PaymenkuService $paymenku): JsonResponse
    {
        $rawBody = $permintaan->getContent();
        $timestamp = (string) $permintaan->header('X-PaymenKu-Timestamp', '');
        $signature = (string) $permintaan->header('X-PaymenKu-Signature', '');

        if (! $paymenku->verifikasiWebhookSignature($rawBody, $timestamp, $signature)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
        }

        $payload = json_decode($rawBody, true);
        if (! is_array($payload)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid JSON'], 400);
        }

        $referenceId = (string) ($payload['reference_id'] ?? '');
        $trxId = (string) ($payload['trx_id'] ?? '');
        $status = strtolower((string) ($payload['status'] ?? ''));
        $isSandbox = (bool) ($payload['is_sandbox'] ?? false);

        $transaksi = TransaksiAd::query()
            ->when($referenceId !== '', fn ($q) => $q->where('reference_id', $referenceId))
            ->when($referenceId === '' && $trxId !== '', fn ($q) => $q->where('trx_id', $trxId))
            ->first();

        if (! $transaksi) {
            return response()->json(['received' => true, 'ignored' => 'transaction_not_found'], 200);
        }

        if ($trxId !== '' && blank($transaksi->trx_id)) {
            $transaksi->update(['trx_id' => $trxId]);
        }

        if ($isSandbox && ! config('services.paymenku.allow_sandbox', true)) {
            return response()->json(['received' => true, 'ignored' => 'sandbox_disabled'], 200);
        }

        if (in_array($status, ['paid', 'expired', 'cancelled', 'failed', 'refunded'], true)) {
            $transaksi->aktifkanIklanJikaLunas(
                $status,
                is_string($payload['paid_at'] ?? null) ? $payload['paid_at'] : null
            );
        }

        return response()->json(['received' => true], 200);
    }
}
