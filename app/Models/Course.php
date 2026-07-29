<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'description',
        'slug',
        'price_mad',
        'cover_image_path',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(CoursePurchase::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function isPurchasedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->purchases()
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->exists();
    }
}

