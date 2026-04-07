<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\TenantSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = TenantSubscription::with(['tenant', 'plan'])
            ->latest()
            ->paginate(20);

        return view('platform.subscriptions.index', compact('subscriptions'));
    }

    public function approve(TenantSubscription $subscription)
    {
        $subscription->update([
            'payment_status' => 'paid',
            'status'         => 'active',
        ]);

        $subscription->tenant->update(['status' => 'active']);

        return back()->with('success', 'Langganan dikonfirmasi.');
    }
}
