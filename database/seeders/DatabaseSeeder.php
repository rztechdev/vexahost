<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VpsSpec;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed & sync VPS product specs
        $this->call(VpsSpecSeeder::class);

        // 2. Single Admin Account (Dynamically Loaded from .env for Security)
        $adminEmail = env('ADMIN_EMAIL', env('SEED_ADMIN_EMAIL', 'vexahosttech@gmail.com'));
        $adminUsername = env('ADMIN_USERNAME', env('SEED_ADMIN_USERNAME', 'mryanrizki11'));
        $adminName = env('ADMIN_NAME', env('SEED_ADMIN_NAME', 'Ryan Rizki'));
        $adminPassword = env('ADMIN_PASSWORD', env('SEED_ADMIN_PASSWORD', '12345678'));
        $adminPhone = env('ADMIN_PHONE', env('SEED_ADMIN_PHONE', env('VEXAHOST_WHATSAPP', '6285774410978')));
        $adminCompany = env('ADMIN_COMPANY', env('SEED_ADMIN_COMPANY', 'VexaHost Cloud Indonesia'));

        $admin = User::firstOrNew(['email' => $adminEmail]);
        $admin->forceFill([
            'username' => $adminUsername,
            'full_name' => $adminName,
            'password' => Hash::make($adminPassword),
            'is_admin' => true,
            'email_verified_at' => now(),
            'channel' => 'website',
            'phone' => $adminPhone,
            'company' => $adminCompany,
            'address' => null,
        ])->save();

        // 3. Seed RBAC + backfill Personal Org untuk existing users.
        $this->call(RolePermissionSeeder::class);
    }
}
