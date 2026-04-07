<?php

namespace App\Livewire\Settings;

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
        $settings = TenantSetting::where('tenant_id', $tenantId)->pluck('value', 'key');

        $this->companyName = $settings->get('company_name', '');
        $this->companyEmail = $settings->get('company_email', '');
        $this->companyPhone = $settings->get('company_phone', '');
        $this->companyAddress = $settings->get('company_address', '');
        $this->timezone = $settings->get('timezone', 'Asia/Jakarta');
        $this->locale = $settings->get('locale', 'id');
        $this->dateFormat = $settings->get('date_format', 'd/m/Y');
        $this->currency = $settings->get('currency', 'IDR');
        $this->featureRecruitment = $settings->get('feature_recruitment', '1') === '1';
        $this->featurePsychTest = $settings->get('feature_psych_test', '1') === '1';
        $this->featurePayroll = $settings->get('feature_payroll', '1') === '1';
        $this->featureLearning = $settings->get('feature_learning', '1') === '1';
        $this->featureEngagement = $settings->get('feature_engagement', '1') === '1';
    }

    public function saveSettings(): void
    {
        $tenantId = auth()->user()->tenant_id;
        $settings = [
            'company_name'       => $this->companyName,
            'company_email'      => $this->companyEmail,
            'company_phone'      => $this->companyPhone,
            'company_address'    => $this->companyAddress,
            'timezone'           => $this->timezone,
            'locale'             => $this->locale,
            'date_format'        => $this->dateFormat,
            'currency'           => $this->currency,
            'feature_recruitment' => $this->featureRecruitment ? '1' : '0',
            'feature_psych_test'  => $this->featurePsychTest ? '1' : '0',
            'feature_payroll'     => $this->featurePayroll ? '1' : '0',
            'feature_learning'    => $this->featureLearning ? '1' : '0',
            'feature_engagement'  => $this->featureEngagement ? '1' : '0',
        ];

        foreach ($settings as $key => $value) {
            TenantSetting::updateOrCreate(
                ['tenant_id' => $tenantId, 'key' => $key],
                ['value' => $value]
            );
        }

        session()->flash('success', 'Pengaturan berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.settings.tenant-settings-manager')
            ->layout('layouts.app', ['pageTitle' => 'Pengaturan']);
    }
}
