import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/app-layout';
import { PageProps, Paginated, Permission, Role, Tenant, User } from '@/types';
import { Head } from '@inertiajs/react';
import { PermissionsTable } from './partials/tables/permissions-table';
import { RolesTable } from './partials/tables/roles-table';
import { TenantsTable } from './partials/tables/tenants-table';
import { UsersTable } from './partials/tables/users-table';

export default function Index({
  users,
  tenants,
  roles,
  permissions,
}: PageProps<{ users: Paginated<User>; tenants: Paginated<Tenant>; roles: Paginated<Role>; permissions: Paginated<Permission> }>) {
  return (
    <AppLayout>
      <Head title="User Management" />
      <div className="p-4 sm:p-6 lg:p-8">
        <div className="sm:flex sm:items-center">
          <div className="sm:flex-auto">
            <h1 className="font-sans text-2xl leading-6 font-semibold">User Management</h1>
            <p className="mt-2 text-sm text-accent-foreground">
              A list of all the users in your account including their name, title, email and role.
            </p>
          </div>
        </div>

        <Tabs defaultValue="users" className="mt-4">
          <TabsList>
            <TabsTrigger value="users">Users</TabsTrigger>
            <TabsTrigger value="tenants">Tenants</TabsTrigger>
            <TabsTrigger value="roles">Roles</TabsTrigger>
            <TabsTrigger value="permissions">Permissions</TabsTrigger>
          </TabsList>
          <TabsContent value="users">
            <UsersTable users={users} />
          </TabsContent>
          <TabsContent value="tenants">
            <TenantsTable tenants={tenants} />
          </TabsContent>
          <TabsContent value="roles">
            <RolesTable roles={roles} />
          </TabsContent>
          <TabsContent value="permissions">
            <PermissionsTable permissions={permissions} />
          </TabsContent>
        </Tabs>
      </div>
    </AppLayout>
  );
}
