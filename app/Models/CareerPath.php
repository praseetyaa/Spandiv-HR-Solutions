<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerPath extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'from_position_id',
        'to_position_id',
        'path_type',
        'avg_years_required',
        'requirements',
    ];

    // ============================================================
    // Relationships
    // ============================================================

    public function fromPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'from_position_id');
    }

    public function toPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'to_position_id');
    }
}
