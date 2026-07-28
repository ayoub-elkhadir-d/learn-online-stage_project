<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            // bank transfer, etc.
            $table->string('payment_method')->nullable();
            $table->string('full_name')->nullable();
            $table->string('rib')->nullable();
            $table->string('receipt_path')->nullable();

            // pending/paid/failed
            $table->string('status')->default('pending')->index();

            $table->timestamp('purchased_at')->nullable();

            // MVP: store a reference number or proof fields if you want later
            $table->string('reference')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_purchases');
    }
};
