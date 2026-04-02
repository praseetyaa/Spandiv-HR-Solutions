<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;

class JobPosition extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id', 'department_id', 'title', 'grade', 'level',
        'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'position_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
