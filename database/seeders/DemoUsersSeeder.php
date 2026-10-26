<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Seed one demo admin and one demo member, including their parent users.
     *
     * Safe to rerun: users are matched by email and child rows by user_id.
     */
    public function run(): void
    {
        // This seeder contains public demo credentials; never create them in production.
        if (! app()->environment('local', 'testing')) {
            $this->command?->warn('DemoUsersSeeder skipped outside local/testing environments.');
            return;
        }

        $now = now();

        DB::transaction(function () use ($now): void {
            DB::table('users')->updateOrInsert(
                ['email' => 'admin@dreampawn.test'],
                [
                    'name' => 'Demo Admin',
                    'password' => Hash::make('AdminDemo123!'),
                    'role' => 'admin',
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            $adminId = DB::table('users')
                ->where('email', 'admin@dreampawn.test')
                ->value('id');

            DB::table('admins')->updateOrInsert(
                ['user_id' => $adminId],
                ['user_id' => $adminId]
            );

            DB::table('users')->updateOrInsert(
                ['email' => 'member@dreampawn.test'],
                [
                    'name' => 'Demo Member',
                    'password' => Hash::make('MemberDemo123!'),
                    'role' => 'member',
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            $memberId = DB::table('users')
                ->where('email', 'member@dreampawn.test')
                ->value('id');

            DB::table('members')->updateOrInsert(
                ['user_id' => $memberId],
                ['user_id' => $memberId]
            );
        });
    }
}
