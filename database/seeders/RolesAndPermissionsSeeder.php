<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed the admin panel roles (super_admin, panel_user) and generate all
     * Shield permissions for the Filament resources. Idempotent — safe to run
     * on every deploy.
     */
    public function run(): void
    {
        $guard = 'web';

        $superAdminRole = Role::firstOrCreate([
            'name' => config('filament-shield.super_admin.name'),
            'guard_name' => $guard,
        ]);

        Role::firstOrCreate([
            'name' => config('filament-shield.panel_user.name'),
            'guard_name' => $guard,
        ]);

        $generated = true;

        try {
            Artisan::call('shield:generate', [
                '--all' => true,
                '--panel' => 'admin',
                '--option' => 'permissions',
            ]);
        } catch (Throwable $e) {
            $generated = false;

            $this->command?->warn(sprintf(
                '[RolesAndPermissionsSeeder] shield:generate failed: %s',
                $e->getMessage(),
            ));
        }

        $permissions = Permission::query()
            ->where('guard_name', $guard)
            ->get();

        if ($generated && $permissions->isNotEmpty()) {
            $this->command?->info(
                "[RolesAndPermissionsSeeder] Synced {$permissions->count()} permissions to {$superAdminRole->name}.",
            );
        }

        $superAdminRole->syncPermissions($permissions);
    }
}
