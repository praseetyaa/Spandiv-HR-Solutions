<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyAcknowledgment extends Model
{
    protected $fillable = [
        'policy_id', 'version_id', 'employee_id', 'acknowledged_at', 'ip_address',
    ];

    protected function casts(): array
    {
        return ['acknowledged_at' => 'datetime'];
    }

    public function policy(): BelongsTo { return $this->belongsTo(CompanyPolicy::class, 'policy_id'); }
    public function version(): BelongsTo { return $this->belongsTo(PolicyVersion::class, 'version_id'); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
}
