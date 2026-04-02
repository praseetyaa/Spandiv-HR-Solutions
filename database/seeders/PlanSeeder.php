<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'          => 'Basic',
                'slug'          => 'basic',
                'price_monthly' => 299000,
                'price_yearly'  => 2990000,
                'max_employees' => 25,
                'max_users'     => 5,
                'description'   => 'Cocok untuk startup dan UMKM dengan karyawan ≤25 orang.',
                'features'      => json_encode([
                    'employee_management',
                    'attendance',
                    'leave_management',
                    'basic_payroll',
                ]),
            ],
            [
                'name'          => 'Professional',
                'slug'          => 'professional',
                'price_monthly' => 799000,
                'price_yearly'  => 7990000,
                'max_employees' => 100,
                'max_users'     => 20,
                'description'   => 'Untuk perusahaan menengah yang butuh HR lengkap.',
                'features'      => json_encode([
                    'employee_management',
                    'attendance',
                    'leave_management',
                    'payroll',
                    'recruitment',
                    'onboarding',
                    'performance',
                    'learning',
                ]),
            ],
            [
                'name'          => 'Enterprise',
                'slug'          => 'enterprise',
                'price_monthly' => 1999000,
                'price_yearly'  => 19990000,
                'max_employees' => 500,
                'max_users'     => 50,
                'description'   => 'Solusi lengkap untuk korporasi besar.',
                'features'      => json_encode([
                    'employee_management',
                    'attendance',
                    'leave_management',
                    'payroll',
                    'recruitment',
                    'psych_test',
                    'onboarding',
                    'performance',
                    'talent_management',
                    'learning',
                    'benefit_expense',
                    'compliance',
                    'wellness',
                    'api_access',
                ]),
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
