<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rolls back the entire HLS/AES-encryption schema addition. Guarded with
 * hasColumn() checks on every column (rather than a bare dropColumn list)
 * because the migrations that originally added these were deleted alongside
 * this one — on an environment where they never actually ran, a plain drop
 * would fail outright instead of no-op.
 */
return new class extends Migration
{
    private const COLUMNS = [
        'status',
        'duration_seconds',
        'hls_path',
        'encryption_key',
        'encryption_key_filename',
        'encryption_status',
        'encryption_algorithm',
        'encoding_error',
    ];

    public function up(): void
    {
        $existing = array_filter(self::COLUMNS, fn (string $column) => Schema::hasColumn('lessons', $column));

        if (empty($existing)) {
            return;
        }

        Schema::table('lessons', function (Blueprint $table) use ($existing) {
            $table->dropColumn($existing);
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('video_path');
            $table->unsignedInteger('duration_seconds')->nullable()->after('status');
            $table->string('hls_path')->nullable()->after('duration_seconds');
            $table->text('encryption_key')->nullable()->after('hls_path');
            $table->string('encryption_key_filename')->nullable()->after('encryption_key');
            $table->string('encryption_status')->default('pending')->after('encryption_key_filename');
            $table->string('encryption_algorithm')->nullable()->after('encryption_status');
            $table->longText('encoding_error')->nullable()->after('encryption_algorithm');
        });
    }
};
