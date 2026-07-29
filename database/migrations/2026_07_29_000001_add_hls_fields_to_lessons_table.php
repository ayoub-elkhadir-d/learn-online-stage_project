<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('video_path');
            $table->unsignedInteger('duration_seconds')->nullable()->after('status');
            $table->string('hls_path')->nullable()->after('duration_seconds');
            $table->text('encryption_key')->nullable()->after('hls_path');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['status', 'duration_seconds', 'hls_path', 'encryption_key']);
        });
    }
};
