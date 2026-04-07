<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Platform admin dashboard
        if ($user->isPlatformUser()) {
            return view('dashboard', [
                'pageTitle'        => 'Platform Dashboard',
                'stats'            => $this->getPlatformStats(),
                'recentActivities' => $this->getRecentActivities(null),
            ]);
        }

        // Tenant dashboard
        return view('dashboard', [
            'pageTitle'        => 'Dashboard',
            'stats'            => $this->getTenantStats($user->tenant_id),
            'recentActivities' => $this->getRecentActivities($user->tenant_id),
        ]);
    }

    /**
     * Get platform-level stats (for super admin).
     */
    private function getPlatformStats(): array
    {
        return [
            [
                'label' => 'Total Tenant',
                'value' => \App\Models\Tenant::count(),
                'icon'  => 'building',
                'color' => 'brand',
            ],
            [
                'label' => 'Total Users',
                'value' => \App\Models\User::count(),
                'icon'  => 'users',
                'color' => 'info',
            ],
            [
                'label' => 'Active Subscriptions',
                'value' => \App\Models\TenantSubscription::where('status', 'active')->count(),
                'icon'  => 'credit-card',
                'color' => 'success',
            ],
            [
                'label' => 'Plans',
                'value' => \App\Models\Plan::where('status', 'active')->count(),
                'icon'  => 'package',
                'color' => 'warning',
            ],
        ];
    }

    /**
     * Get tenant-level stats.
     */
    private function getTenantStats(int $tenantId): array
    {
        return [
            [
                'label' => 'Total Karyawan',
                'value' => \DB::table('employees')->where('tenant_id', $tenantId)->count(),
                'icon'  => 'users',
                'color' => 'brand',
            ],
            [
                'label' => 'Hadir Hari Ini',
                'value' => \DB::table('attendances')
                    ->where('tenant_id', $tenantId)
                    ->whereDate('date', today())
                    ->count(),
                'icon'  => 'clock',
                'color' => 'success',
            ],
            [
                'label' => 'Cuti Pending',
                'value' => \DB::table('leave_requests')
                    ->where('tenant_id', $tenantId)
                    ->where('status', 'pending')
                    ->count(),
                'icon'  => 'calendar',
                'color' => 'warning',
            ],
            [
                'label' => 'Departemen',
                'value' => \DB::table('departments')->where('tenant_id', $tenantId)->count(),
                'icon'  => 'grid',
                'color' => 'info',
            ],
        ];
    }

    /**
     * Get recent audit log activities.
     */
    private function getRecentActivities(?int $tenantId): array
    {
        $query = DB::table('audit_logs')
            ->select('action', 'model_type', 'model_id', 'created_at')
            ->orderByDesc('created_at')
            ->limit(10);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $logs = $query->get();

        if ($logs->isEmpty()) {
            return [
                ['text' => 'Sistem dimulai — Selamat datang!', 'time' => 'Baru saja', 'icon' => '🚀'],
                ['text' => 'Database berhasil di-seed', 'time' => 'Baru saja', 'icon' => '🗃️'],
                ['text' => 'Konfigurasi selesai', 'time' => 'Baru saja', 'icon' => '⚙️'],
            ];
        }

        $icons = [
            'created' => '✅', 'updated' => '✏️', 'deleted' => '🗑️',
            'login' => '🔑', 'approved' => '👍', 'rejected' => '❌',
        ];

        $actionLabels = [
            'created' => 'Ditambahkan', 'updated' => 'Diperbarui', 'deleted' => 'Dihapus',
            'login' => 'Login', 'approved' => 'Disetujui', 'rejected' => 'Ditolak',
        ];

        return $logs->map(fn ($log) => [
            'text' => ($actionLabels[$log->action] ?? ucfirst($log->action)) . ' — ' . class_basename($log->model_type ?? ''),
            'time' => \Carbon\Carbon::parse($log->created_at)->diffForHumans(),
            'icon' => $icons[$log->action] ?? '📋',
        ])->toArray();
    }
}
