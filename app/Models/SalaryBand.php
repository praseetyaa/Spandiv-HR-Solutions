<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryBand extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'grade_id',
        'min_salary',
        'mid_salary',
        'max_salary',
        'effective_date',
    ];

    protected function casts(): array
    {
        return [
            'min_salary'     => 'decimal:2',
            'mid_salary'     => 'decimal:2',
            'max_salary'     => 'decimal:2',
            'effective_date' => 'date',
        ];
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class, 'grade_id');
    }
}
