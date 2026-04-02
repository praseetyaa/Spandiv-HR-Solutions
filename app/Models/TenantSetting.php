<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'logo_path', 'brand_color', 'timezone', 'currency',
        'language', 'date_format', 'payroll_cutoff_day',
        'company_address', 'company_phone', 'company_email', 'npwp_perusahaan',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
