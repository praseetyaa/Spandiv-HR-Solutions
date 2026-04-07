<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\TenantSetting;
use App\Models\TenantSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TenantProvisioningService
{
    public function provision(array $data): Tenant
    {
        $plan = Plan::findOrFail($data['plan_id']);

        $tenant = Tenant::create([
            'plan_id'       => $plan->id,
            'name'          => $data['company_name'],
            'slug'          => \Illuminate\Support\Str::slug($data['company_name']),
            'subdomain'     => $data['subdomain'],
            'domain'        => $data['domain'] ?? null,
            'status'        => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        // Create settings
        TenantSetting::create([
            'tenant_id'   => $tenant->id,
            'brand_color' => $data['brand_color'] ?? '#2B5BA8',
            'timezone'    => $data['timezone'] ?? 'Asia/Jakarta',
        ]);

        // Create subscription
        TenantSubscription::create([
            'tenant_id'      => $tenant->id,
            'plan_id'        => $plan->id,
            'starts_at'      => now(),
            'ends_at'        => now()->addDays(14),
            'billing_cycle'  => 'monthly',
            'payment_status' => 'unpaid',
            'status'         => 'active',
        ]);

        // Create owner account
        $owner = User::create([
            'tenant_id' => $tenant->id,
            'name'      => $data['owner_name'],
            'email'     => $data['owner_email'],
            'password'  => Hash::make($data['owner_password']),
            'guard'     => 'web',
            'is_active' => true,
        ]);

        $owner->assignRole('company_owner');

        return $tenant;
    }

    public function suspend(Tenant $tenant): void
    {
        $tenant->update(['status' => 'suspended']);
        User::where('tenant_id', $tenant->id)->update(['is_active' => false]);
    }

    public function activate(Tenant $tenant): void
    {
        $tenant->update(['status' => 'active']);
        User::where('tenant_id', $tenant->id)->update(['is_active' => true]);
    }
}
