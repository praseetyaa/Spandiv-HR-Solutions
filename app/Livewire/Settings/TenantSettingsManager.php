<?php

namespace App\Livewire\Settings;

use App\Models\Tenant;
use App\Models\TenantSetting;
use Livewire\Component;

class TenantSettingsManager extends Component
{
    public string $companyName = '';
    public string $companyEmail = '';
    public string $companyPhone = '';
    public string $companyAddress = '';
    public string $timezone = 'Asia/Jakarta';
    public string $locale = 'id';
    public string $dateFormat = 'd/m/Y';
    public string $currency = 'IDR';

    // Feature toggles
    public bool $featureRecruitment = true;
    public bool $featurePsychTest = true;
    public bool $featurePayroll = true;
    public bool $featureLearning = true;
    public bool $featureEngagement = true;

    public function mount(): void
    {
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        $tenantId = auth()->user()->tenant_id;
        $setting = TenantSetting::where('tenant_id', $tenantId)->first();
        $tenant = Tenant::find($tenantId);

        $this->companyName = $tenant->name ?? '';
        $this->companyEmail = $setting->company_email ?? '';
        $this->companyPhone = $setting->company_phone ?? '';
        $this->companyAddress = $setting->company_address ?? '';
        $this->timezone = $setting->timezone ?? 'Asia/Jakarta';
        $this->locale = $setting->language ?? 'id';
        $this->dateFormat = $setting->date_format ?? 'd/m/Y';
        $this->currency = $setting->currency ?? 'IDR';

        // Feature toggles are not columns in the current table,
        // so keep defaults for now.
    }

    public function saveSettings(): void
    {
        $tenantId = auth()->user()->tenant_id;

        TenantSetting::updateOrCreate(
            ['tenant_id' => $tenantId],
            [
                'company_email'   => $this->companyEmail,
                'company_phone'   => $this->companyPhone,
                'company_address' => $this->companyAddress,
                'timezone'        => $this->timezone,
                'language'        => $this->locale,
                'date_format'     => $this->dateFormat,
                'currency'        => $this->currency,
            ]
        );

        // Update tenant name
        Tenant::where('id', $tenantId)->update(['name' => $this->companyName]);

        session()->flash('success', 'Pengaturan berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.settings.tenant-settings-manager')
            ->layout('layouts.app', ['pageTitle' => 'Pengaturan']);
    }
}

