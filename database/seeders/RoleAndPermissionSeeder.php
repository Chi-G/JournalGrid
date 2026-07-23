<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'voucher.create',
            'voucher.post',
            'voucher.reverse',
            'voucher.view',
            'chart-of-accounts.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $accountant = Role::firstOrCreate(['name' => 'Accountant']);
        $accountant->syncPermissions(['voucher.create', 'voucher.view']);

        $approver = Role::firstOrCreate(['name' => 'Approver']);
        $approver->syncPermissions(['voucher.post', 'voucher.reverse', 'voucher.view']);

        $auditor = Role::firstOrCreate(['name' => 'Auditor']);
        $auditor->syncPermissions(['voucher.view']);

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions(['voucher.view', 'chart-of-accounts.manage', 'voucher.create', 'voucher.post', 'voucher.reverse']);
    }
}
