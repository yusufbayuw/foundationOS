<?php

namespace App\Console\Commands;

use App\Support\TypedValue;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Modules\Core\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Signature('fos:make-platform-owner {--user= : User ID or email}')]
#[Description('Assign the platform_owner role to a user, granting access to the /platform panel')]
class MakePlatformOwner extends Command
{
    public function handle(): int
    {
        $identifier = $this->option('user');

        if (! $identifier) {
            $identifier = $this->ask('User ID or email');
        }

        $user = is_numeric($identifier)
            ? User::find($identifier)
            : User::where('email', $identifier)->first();

        if (! $user) {
            $this->error('User not found: '.TypedValue::string($identifier));

            return self::FAILURE;
        }

        $role = Role::firstOrCreate(
            ['name' => 'platform_owner', 'guard_name' => 'web'],
        );

        // Platform roles use tenant_id 0 as global SaaS scope (see User::canAccessPanel).
        setPermissionsTeamId(0);

        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => 0,
            ],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info("✓ User [{$user->name} <{$user->email}>] assigned platform_owner role.");
        $this->line('  They can now access the platform panel at: '.url('/platform'));

        return self::SUCCESS;
    }
}
