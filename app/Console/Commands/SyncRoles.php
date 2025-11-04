<?php

namespace App\Console\Commands;

use App\Enums\Workspace\Capabilities;
use App\Enums\Workspace\RoleEnum;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize roles with the latest definitions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Always clear cached permissions to avoid stale mappings
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->syncPermissions();
        $this->syncRoles();

        // Clear again after changes
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('Roles synchronized successfully.');
        return self::SUCCESS;
    }

    protected function syncPermissions(): void
    {
        foreach (Capabilities::cases() as $capability) {
            Permission::updateOrCreate(
                ['name' => $capability->value, 'guard_name' => 'web'],
                [] // no extra attrs for now
            );
            $this->info("Permission '{$capability->value}' synchronized.");
        }
    }

    protected function syncRoles(): void
    {
        // Define role → capabilities mapping
        $roleCapabilities = [
            RoleEnum::OWNER->value => Capabilities::cases(),
            RoleEnum::ADMIN->value => [
                Capabilities::MEMBER_SHOW,
                Capabilities::MEMBER_INVITE,
                Capabilities::MEMBER_REMOVE,
                Capabilities::MEMBER_CHANGE_ROLE,
                Capabilities::WORKSPACE_UPDATE,
            ],
            RoleEnum::MEMBER->value => [
                Capabilities::MEMBER_SHOW,
            ],
        ];

        foreach ($roleCapabilities as $roleName => $caps) {
            // Ensure the role exists
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            // Map Enum cases to their string values
            $permissionNames = array_map(
                fn (Capabilities $c) => $c->value,
                $caps
            );

            // Ensure all permissions exist (idempotent safety)
            foreach ($permissionNames as $perm) {
                Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }

            // Sync role permissions
            $role->syncPermissions($permissionNames);

            $this->info("Role '{$roleName}' synced with " . count($permissionNames) . ' permissions.');
        }
    }
}
