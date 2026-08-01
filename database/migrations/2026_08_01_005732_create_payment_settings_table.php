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
        // Deliberately a singleton table: the app only ever reads/writes the
        // single row via PaymentSetting::current() — there is no route or UI
        // path that creates a second row, so "only one active configuration"
        // holds by construction rather than needing an is_active flag.
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('account_holder');
            $table->string('account_number');
            $table->string('iban');
            $table->string('swift_bic');
            $table->text('payment_instructions')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('support_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
