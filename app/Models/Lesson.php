<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_READY = 'ready';
    public const STATUS_FAILED = 'failed';

    public const ENCRYPTION_PENDING = 'pending';
    public const ENCRYPTION_ENCRYPTED = 'encrypted';
    public const ENCRYPTION_FAILED = 'failed';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'video_path',
        'sort_order',
        'status',
        'duration_seconds',
        'hls_path',
        'encryption_key',
        'encryption_key_filename',
        'encryption_status',
        'encryption_algorithm',
        'encoding_error',
    ];

    protected $casts = [
        'encryption_key' => 'encrypted',
        'encoding_error' => 'array',
    ];

    protected $hidden = [
        'encryption_key',
        'encryption_key_filename',
        'video_path',
        'hls_path',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isReady(): bool
    {
        return $this->status === self::STATUS_READY;
    }
}
