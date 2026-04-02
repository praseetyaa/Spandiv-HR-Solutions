<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseItem extends Model
{
    protected $fillable = [
        'request_id', 'category_id', 'description', 'amount', 'receipt_path', 'item_date',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'item_date' => 'date'];
    }

    public function request(): BelongsTo { return $this->belongsTo(ExpenseRequest::class, 'request_id'); }
    public function category(): BelongsTo { return $this->belongsTo(ExpenseCategory::class, 'category_id'); }
}
