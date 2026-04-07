<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDetail extends Model
{
    protected $fillable = [
        'employee_id', 'gender', 'birth_date', 'birth_place', 'religion',
        'marital_status', 'tax_status', 'npwp', 'bpjs_kesehatan',
        'bpjs_ketenagakerjaan', 'bank_name', 'bank_account',
        'bank_account_name', 'address', 'city', 'province', 'postal_code',
        'education_level', 'education_major', 'education_institution',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'npwp'       => 'encrypted',
            'bank_account' => 'encrypted',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }

    public function getMaritalLabelAttribute(): string
    {
        return match ($this->marital_status) {
            'single'   => 'Belum Menikah',
            'married'  => 'Menikah',
            'divorced' => 'Cerai',
            'widowed'  => 'Duda/Janda',
            default    => '-',
        };
    }

    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            'male'   => 'Laki-laki',
            'female' => 'Perempuan',
            default  => '-',
        };
    }
}
