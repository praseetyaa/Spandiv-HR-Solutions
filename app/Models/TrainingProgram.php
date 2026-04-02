<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingProgram extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'name', 'description', 'category', 'max_participants', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TrainingSchedule::class, 'program_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
