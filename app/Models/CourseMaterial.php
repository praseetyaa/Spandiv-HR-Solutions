<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMaterial extends Model
{
    protected $fillable = [
        'section_id', 'title', 'type', 'file_path', 'video_url', 'duration_minutes', 'order_number',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'video'        => '🎬',
            'pdf'          => '📄',
            'presentation' => '📊',
            'link'         => '🔗',
            'quiz'         => '📝',
            default        => '📁',
        };
    }
}
