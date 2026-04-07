<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    protected $fillable = [
        'employee_id', 'type', 'name', 'file_path', 'original_name',
        'mime_type', 'file_size', 'expires_at', 'is_verified',
        'verified_by', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'  => 'date',
            'verified_at' => 'datetime',
            'is_verified' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'ktp'        => 'KTP',
            'npwp'       => 'NPWP',
            'ijazah'     => 'Ijazah',
            'sertifikat' => 'Sertifikat',
            'foto'       => 'Foto',
            'kontrak'    => 'Kontrak',
            'other'      => 'Lainnya',
            default      => $this->type,
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
