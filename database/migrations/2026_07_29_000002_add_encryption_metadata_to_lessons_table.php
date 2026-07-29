<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('encryption_key_filename')->nullable()->after('encryption_key');
            $table->string('encryption_status')->default('pending')->after('encryption_key_filename');
            $table->string('encryption_algorithm')->nullable()->after('encryption_status');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['encryption_key_filename', 'encryption_status', 'encryption_algorithm']);
        });
    }
};
