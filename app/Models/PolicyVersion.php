<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PolicyVersion extends Model
{
    protected $fillable = [
        'policy_id', 'version_number', 'content', 'file_path',
        'created_by', 'effective_date', 'is_current',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function policy(): BelongsTo { return $this->belongsTo(CompanyPolicy::class, 'policy_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function acknowledgments(): HasMany { return $this->hasMany(PolicyAcknowledgment::class, 'version_id'); }
}
