<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::transaction(function () {
            $this->createSuperAdmin();
            $this->createTenantsWithUsersAndRoles();
        });
    }

    /**
     * Create the Super Admin user.
     * The user's universal access is granted via the AuthServiceProvider.
     */
    private function createSuperAdmin(): void
    {
        // Create a Super Admin user
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    /**
     * Create tenants, and for each tenant, create roles, permissions, and users.
     */
    private function createTenantsWithUsersAndRoles(): void
    {
        $tenantCount = 3;
        $usersPerTenant = 5;

        Tenant::factory()->count($tenantCount)->create()->each(function (Tenant $tenant) use ($usersPerTenant) {
            $this->command->info("Seeding tenant #{$tenant->id}: {$tenant->name}");

            // Create tenant-specific roles using firstOrCreate to be tenant-aware
            $adminRole = Role::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => 'admin'],
                ['guard_name' => 'web']
            );
            $userRole = Role::firstOrCreate(
                ['tenant_id' => $tenant->id, 'name' => 'user'],
                ['guard_name' => 'web']
            );

            // Create tenant-specific permissions
            $permissions = [
                // User permissions
                'view users', 'create users', 'edit users', 'delete users',
                // Post permissions
                'view posts', 'create posts', 'edit posts', 'delete posts',
            ];

            foreach ($permissions as $permission) {
                Permission::firstOrCreate(
                    ['tenant_id' => $tenant->id, 'name' => $permission],
                    ['guard_name' => 'web']
                );
            }

            // Manually attach permissions to roles with the tenant_id
            $allPermissions = Permission::where('tenant_id', $tenant->id)->get();
            $adminRole->permissions()
                ->attach($allPermissions->pluck('id'), ['tenant_id' => $tenant->id]);

            $userPermissions = Permission::where('tenant_id', $tenant->id)
                ->whereIn('name', ['view posts', 'create posts'])
                ->get();
            $userRole->permissions()
                ->attach($userPermissions->pluck('id'), ['tenant_id' => $tenant->id]);

            // Create an Admin user for the tenant
            $adminUser = User::factory()->create([
                'name' => "Admin User ({$tenant->name})",
                'email' => Str::slug($tenant->name).'@admin.com',
                'password' => Hash::make('password'),
            ]);

            $adminUser->tenants()->attach($tenant->id);
            $adminUser->assignRoleInTenant($adminRole, $tenant);
            $this->command->info("  - Created Admin: {$adminUser->email}");

            // Create Regular users for the tenant
            User::factory()->count($usersPerTenant)
                ->create()
                ->each(
                    function (User $user) use ($tenant, $userRole) {
                        $user->tenants()->attach($tenant->id);
                        $user->assignRoleInTenant($userRole, $tenant);
                    }
                );
            $this->command->info("  - Created {$usersPerTenant} Regular Users");
        });
    }
}
