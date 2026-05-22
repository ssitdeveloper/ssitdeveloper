<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AdminUser;
use App\Models\Subscription;
use App\Enums\UserRole;
use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'phone' => '+91-9876543210',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create AdminUser record for the user (required for admin login)
        AdminUser::firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'role' => 'admin',
                'permissions' => null,
                'is_active' => true,
            ]
        );

        // Create subscription for admin user
        Subscription::firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'plan' => SubscriptionPlan::PREMIUM,
                'status' => SubscriptionStatus::ACTIVE,
                'started_at' => now(),
                'expires_at' => now()->addYear(),
                'auto_renew' => true,
            ]
        );

        // Create Student User
        $studentUser = User::firstOrCreate(
            ['email' => 'student@test.com'],
            [
                'name' => 'Student User',
                'phone' => '+91-9123456789',
                'password' => Hash::make('password'),
                'role' => UserRole::STUDENT,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create subscription for student user
        Subscription::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'plan' => SubscriptionPlan::MONTHLY,
                'status' => SubscriptionStatus::ACTIVE,
                'started_at' => now(),
                'expires_at' => now()->addDays(30),
                'auto_renew' => false,
            ]
        );

        $this->command->info('✓ Admin and Student users created successfully!');
        $this->command->line('Admin Email: admin@test.com | Password: password | Plan: Premium');
        $this->command->line('Student Email: student@test.com | Password: password | Plan: Monthly');
    }
}
