<?php

namespace App\Console\Commands;

use App\Models\User;
use Filament\Panel;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class GrantAdminRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Idempotent: safe to run repeatedly, and on every deploy.
     */
    protected $signature = 'admin:grant {email : Email of the user to grant panel access} {--role=super_admin : Admin panel role to assign}';

    /**
     * The console command description.
     */
    protected $description = 'Grant an admin panel role to an existing user (idempotent)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $roleName = (string) $this->option('role');

        if (! in_array($roleName, (array) config('auth.admin_roles', []), true)) {
            $this->error(sprintf(
                'Role [%s] is not a configured admin panel role. Allowed: %s',
                $roleName,
                implode(', ', (array) config('auth.admin_roles', [])),
            ));

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No user found with email [{$email}].");

            return self::FAILURE;
        }

        $role = Role::findOrCreate($roleName, 'web');
        $user->assignRole($role);

        $this->info(sprintf(
            'User [%s] now holds role [%s]. Panel access: %s',
            $user->email,
            $roleName,
            $user->canAccessPanel(app(Panel::class)) ? 'GRANTED' : 'DENIED',
        ));

        return self::SUCCESS;
    }
}
