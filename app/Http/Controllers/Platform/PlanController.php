<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('tenants')->orderBy('price')->get();
        return view('platform.plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'slug'             => 'required|string|max:50|unique:plans,slug',
            'price'            => 'required|numeric|min:0',
            'billing_cycle'    => 'required|in:monthly,yearly',
            'max_employees'    => 'required|integer|min:1',
            'max_users'        => 'required|integer|min:1',
            'features'         => 'nullable|array',
            'is_active'        => 'boolean',
        ]);

        Plan::create($validated);

        return back()->with('success', 'Plan berhasil dibuat.');
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name'             => 'sometimes|string|max:100',
            'price'            => 'sometimes|numeric|min:0',
            'max_employees'    => 'sometimes|integer|min:1',
            'max_users'        => 'sometimes|integer|min:1',
            'features'         => 'nullable|array',
            'is_active'        => 'boolean',
        ]);

        $plan->update($validated);
        return back()->with('success', 'Plan berhasil diperbarui.');
    }
}
