<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseAsset extends Model
{
    /**
     * Recognized atmlkolktachment types — drives which icon the timeline card
     * renders. 'text' means a plain announcement with no attachment.
     */
    public const TYPES = [
        'text', 'image', 'pdf', 'zip', 'rar', 'word', 'excel',
        'powerpoint', 'video', 'audio', 'link',
    ];

    protected $fillable = [
        'course_id',
        'user_id',
        'type',
        'message',
        'file_path',
        'original_filename',
        'file_size_bytes',
        'external_url',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Which <x-icon> to render for this attachment type on the timeline.
     */
    public function iconName(): string
    {
        return match ($this->type) {
            'image' => 'image',
            'pdf', 'word' => 'file-text',
            'zip', 'rar' => 'archive',
            'excel' => 'file-spreadsheet',
            'powerpoint' => 'presentation',
            'video' => 'video',
            'audio' => 'music',
            'link' => 'link',
            default => 'megaphone',
        };
    }

    public function humanFileSize(): ?string
    {
        if (! $this->file_size_bytes) {
            return null;
        }

        $bytes = (float) $this->file_size_bytes;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1) . ' ' . $units[$i];
    }
}
