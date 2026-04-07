<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants'    => Tenant::count(),
            'active_tenants'   => Tenant::where('status', 'active')->count(),
            'trial_tenants'    => Tenant::where('status', 'trial')->count(),
            'suspended_tenants'=> Tenant::where('status', 'suspended')->count(),
            'total_users'      => User::where('guard', 'web')->count(),
            'total_employees'  => Employee::count(),
            'mrr'              => $this->calculateMRR(),
        ];

        $recentTenants = Tenant::latest()->limit(10)->get();

        return view('platform.dashboard', compact('stats', 'recentTenants'));
    }

    private function calculateMRR(): float
    {
        return Tenant::where('status', 'active')
            ->with('plan')
            ->get()
            ->sum(fn ($t) => $t->plan?->price_monthly ?? 0);
    }
}
