<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_ads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lowongan_id')->constrained('lowongans')->cascadeOnDelete();
            $table->string('reference_id', 100)->unique();
            $table->string('trx_id', 100)->nullable()->index();
            $table->string('idempotency_key', 100)->nullable();
            $table->string('paket', 30);
            $table->unsignedInteger('durasi_hari');
            $table->unsignedBigInteger('budget_tambahan')->default(0);
            $table->unsignedBigInteger('amount_dasar');
            $table->decimal('amount_bayar', 14, 2)->nullable();
            $table->string('channel_code', 50);
            $table->string('channel_name', 100)->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->boolean('is_sandbox')->default(true);
            $table->text('pay_url')->nullable();
            $table->text('simulator_url')->nullable();
            $table->json('payment_info')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_ads');
    }
};
