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
        $superAdmin = User::create([
            'tenant_id' => null,
            'name'      => 'Super Admin',
            'email'     => 'admin@hrapp.id',
            'password'  => Hash::make('password'),
            'guard'     => 'platform',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super_admin');

        // Support Admin — platform level
        $supportAdmin = User::create([
            'tenant_id' => null,
            'name'      => 'Support Admin',
            'email'     => 'support@hrapp.id',
            'password'  => Hash::make('password'),
            'guard'     => 'platform',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $supportAdmin->assignRole('support_admin');
    }
}
