<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'title', 'description', 'category', 'level',
        'duration_minutes', 'thumbnail_path', 'is_mandatory', 'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_mandatory' => 'boolean',
            'is_active'    => 'boolean',
        ];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('order_number');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDurationHoursAttribute(): string
    {
        $h = intdiv($this->duration_minutes, 60);
        $m = $this->duration_minutes % 60;
        return $h > 0 ? "{$h}j {$m}m" : "{$m}m";
    }

    public function getEnrolledCountAttribute(): int
    {
        return $this->enrollments()->count();
    }

    public function getCompletedCountAttribute(): int
    {
        return $this->enrollments()->where('status', 'completed')->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
