<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hand off the legacy `is_admin` boolean to the new Super Admin role,
     * then remove the column. This migration is run-once and self-contained:
     * it ensures the role row exists (creating a minimal stub if the seeder
     * hasn't run yet) so we don't strand any existing admins.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_admin')) {
            return;
        }

        // 1) Make sure the Super Admin role row exists. The RoleSeeder will
        //    upsert the cosmetic fields (display_name/color/permissions) later
        //    on if it hasn't already run.
        $superAdminId = (int) (DB::table('roles')
            ->where('name', 'Super Admin')
            ->where('guard_name', 'web')
            ->value('id') ?? 0);

        if ($superAdminId === 0) {
            $superAdminId = (int) DB::table('roles')->insertGetId([
                'name' => 'Super Admin',
                'guard_name' => 'web',
                'display_name' => 'Super Admin',
                'description' => 'Unrestricted access. Bypasses all permission checks.',
                'color' => 'rose',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2) Promote every legacy admin into that role via the pivot table.
        //    We do this in raw SQL because the Spatie cached registrar isn't
        //    yet aware of our extended model bindings at migration time.
        $legacyAdminIds = DB::table('users')->where('is_admin', true)->pluck('id');
        foreach ($legacyAdminIds as $userId) {
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id' => $superAdminId,
                    'model_type' => User::class,
                    'model_id' => $userId,
                ],
                [],
            );
        }

        // 3) Drop the column now that nobody depends on it.
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        // Restore the boolean for anyone still holding Super Admin.
        $superAdminId = (int) (DB::table('roles')
            ->where('name', 'Super Admin')
            ->where('guard_name', 'web')
            ->value('id') ?? 0);

        if ($superAdminId !== 0) {
            $userIds = DB::table('model_has_roles')
                ->where('role_id', $superAdminId)
                ->where('model_type', User::class)
                ->pluck('model_id');

            if ($userIds->isNotEmpty()) {
                DB::table('users')->whereIn('id', $userIds)->update(['is_admin' => true]);
            }
        }
    }
};
