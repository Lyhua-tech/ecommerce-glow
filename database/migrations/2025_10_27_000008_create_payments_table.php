<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // A unique ID you generate locally to send to the payment gateway.
            // This prevents duplicate requests and helps you find the record
            // before you even get a response.
            $table->uuid('uuid')->unique();

            // Foreign key to the order this payment is for.
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            // Payment details
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD'); // e.g., "USD", "KHR"

            // The payment channel the user selected.
            $table->enum('payment_method', ['card', 'wechat', 'abaqr', 'khqr', 'abapay']);

            // The lifecycle of this specific transaction attempt.
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');

            // --- Gateway Specific Fields ---

            // The Transaction ID provided BY the payment gateway (e.g., ABA's `tran_id`).
            // Nullable because it's empty until the gateway provides it.
            // Indexed for fast lookups when handling webhooks.
            $table->string('gateway_transaction_id')->nullable()->index();

            // The URL to redirect the user to for payment.
            $table->text('payment_redirect_url')->nullable();

            // A place to store the full response from the gateway (e.g., the webhook/IPN).
            // Invaluable for debugging what PayWay told your server.
            $table->json('gateway_response_log')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
