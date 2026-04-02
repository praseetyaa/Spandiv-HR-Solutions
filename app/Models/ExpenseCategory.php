<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'name', 'code', 'max_amount', 'requires_receipt', 'requires_approval', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_amount'        => 'decimal:2',
            'requires_receipt'  => 'boolean',
            'requires_approval' => 'boolean',
            'is_active'         => 'boolean',
        ];
    }

    public function expenseItems(): HasMany { return $this->hasMany(ExpenseItem::class, 'category_id'); }
    public function scopeActive($query) { return $query->where('is_active', true); }
}
