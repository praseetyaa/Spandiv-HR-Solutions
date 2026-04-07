<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Plan;
use App\Services\TenantProvisioningService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        return view('platform.tenants.index');
    }

    public function create()
    {
        $plans = Plan::where('is_active', true)->orderBy('price')->get();
        return view('platform.tenants.create', compact('plans'));
    }

    public function store(Request $request, TenantProvisioningService $provisioning)
    {
        $validated = $request->validate([
            'company_name'   => 'required|string|max:255',
            'subdomain'      => 'required|string|max:50|unique:tenants,subdomain',
            'domain'         => 'nullable|string|max:255',
            'plan_id'        => 'required|exists:plans,id',
            'owner_name'     => 'required|string|max:255',
            'owner_email'    => 'required|email|unique:users,email',
            'owner_password' => 'required|string|min:8',
            'brand_color'    => 'nullable|string|max:7',
            'timezone'       => 'nullable|string|max:50',
        ]);

        $tenant = $provisioning->provision($validated);

        return redirect()->route('platform.tenants.index')
            ->with('success', "Tenant {$tenant->name} berhasil dibuat.");
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['plan', 'subscription', 'settings']);
        $employeeCount = \App\Models\Employee::where('tenant_id', $tenant->id)->count();
        $userCount = \App\Models\User::where('tenant_id', $tenant->id)->count();

        return view('platform.tenants.show', compact('tenant', 'employeeCount', 'userCount'));
    }

    public function suspend(Tenant $tenant, TenantProvisioningService $provisioning)
    {
        $provisioning->suspend($tenant);
        return back()->with('success', "Tenant {$tenant->name} telah di-suspend.");
    }

    public function activate(Tenant $tenant, TenantProvisioningService $provisioning)
    {
        $provisioning->activate($tenant);
        return back()->with('success', "Tenant {$tenant->name} telah diaktifkan.");
    }
}
