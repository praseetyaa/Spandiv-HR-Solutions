<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function start(Tenant $tenant)
    {
        $owner = User::where('tenant_id', $tenant->id)
            ->whereHas('roles', fn ($q) => $q->where('name', 'company_owner'))
            ->first();

        if (!$owner) {
            return back()->with('error', 'Tenant tidak memiliki owner account.');
        }

        session()->put('impersonating_from', Auth::id());
        session()->put('impersonating_tenant', $tenant->id);

        Auth::login($owner);

        return redirect()->route('dashboard')
            ->with('info', "Anda sedang mengimpersonasi {$tenant->name}.");
    }

    public function stop()
    {
        $adminId = session()->pull('impersonating_from');

        if ($adminId) {
            $admin = User::findOrFail($adminId);
            Auth::login($admin);
            session()->forget('impersonating_tenant');
        }

        return redirect()->route('platform.dashboard')
            ->with('info', 'Kembali ke Platform Admin.');
    }
}
