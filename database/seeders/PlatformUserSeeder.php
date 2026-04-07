<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlatformUserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin — platform level
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@hrapp.id'],
            [
                'tenant_id' => null,
                'name'      => 'Super Admin',
                'password'  => Hash::make('password'),
                'guard'     => 'platform',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }

        // Support Admin — platform level
        $supportAdmin = User::firstOrCreate(
            ['email' => 'support@hrapp.id'],
            [
                'tenant_id' => null,
                'name'      => 'Support Admin',
                'password'  => Hash::make('password'),
                'guard'     => 'platform',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if (!$supportAdmin->hasRole('support_admin')) {
            $supportAdmin->assignRole('support_admin');
        }
    }
}
