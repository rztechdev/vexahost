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
        $adminEmail = env('ADMIN_EMAIL', 'admin@vexahost.com');
        $adminUsername = env('ADMIN_USERNAME', 'admin');
        $adminName = env('ADMIN_NAME', 'Ryan Rizki');
        $adminPassword = env('ADMIN_PASSWORD', '12345678');
        $adminCompany = env('ADMIN_COMPANY', 'VexaHost Cloud Indonesia');

        $admin = User::firstOrNew(['email' => $adminEmail]);
        $admin->forceFill([
            'username' => $adminUsername,
            'full_name' => $adminName,
            'password' => Hash::make($adminPassword),
            'is_admin' => true,
            'channel' => 'website',
            'phone' => env('ADMIN_PHONE', null),
            'company' => $adminCompany,
            'address' => null,
        ])->save();

        // 3. Seed RBAC + backfill Personal Org untuk existing users.
        $this->call(RolePermissionSeeder::class);
    }
}
