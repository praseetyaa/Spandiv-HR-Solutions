<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'month', 'year', 'pay_date', 'status',
        'processed_by', 'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'pay_date'     => 'date',
            'processed_at' => 'datetime',
        ];
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class, 'period_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getPeriodLabelAttribute(): string
    {
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return ($months[$this->month] ?? '') . ' ' . $this->year;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'      => 'Draft',
            'processing' => 'Diproses',
            'completed'  => 'Selesai',
            'cancelled'  => 'Dibatalkan',
            default      => $this->status,
        };
    }
}
