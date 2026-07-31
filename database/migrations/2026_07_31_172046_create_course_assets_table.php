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
        Schema::create('course_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            // Who posted it (the instructor/admin) — nullable since the
            // posting UI doesn't exist yet; rows will be seeded/inserted
            // directly until the admin panel for this ships.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Drives which icon renders and whether it's a file, a link, or
            // a plain text-only announcement.
            $table->string('type');

            $table->text('message')->nullable();
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->string('external_url')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_assets');
    }
};
