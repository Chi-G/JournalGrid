<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            ChartOfAccountsSeeder::class,
        ]);

        $accountant = User::firstOrCreate(
            ['email' => 'accountant@journalgrid.com'],
            ['name' => 'John Accountant', 'password' => Hash::make('password')]
        );
        $accountant->assignRole('Accountant');

        $approver = User::firstOrCreate(
            ['email' => 'approver@journalgrid.com'],
            ['name' => 'Alice Approver', 'password' => Hash::make('password')]
        );
        $approver->assignRole('Approver');

        $auditor = User::firstOrCreate(
            ['email' => 'auditor@journalgrid.com'],
            ['name' => 'Arthur Auditor', 'password' => Hash::make('password')]
        );
        $auditor->assignRole('Auditor');

        $admin = User::firstOrCreate(
            ['email' => 'admin@journalgrid.com'],
            ['name' => 'Admin User', 'password' => Hash::make('password')]
        );
        $admin->assignRole('Admin');

        $this->call(JournalVoucherSeeder::class);
    }
}
