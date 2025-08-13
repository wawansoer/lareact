import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Paginated, Role } from '@/types';
import { router } from '@inertiajs/react';
import { RoleForm } from '../forms/role-form';

export function RolesTable({ roles }: { roles: Paginated<Role> }) {
  return (
    <Card>
      <CardHeader>
        <div className="flex justify-between">
          <CardTitle>Roles</CardTitle>
          <RoleForm>
            <Button>Create Role</Button>
          </RoleForm>
        </div>
      </CardHeader>
      <CardContent>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Name</TableHead>
              <TableHead />
            </TableRow>
          </TableHeader>
          <TableBody>
            {roles.data.map((role) => (
              <TableRow key={role.id}>
                <TableCell>{role.name}</TableCell>
                <TableCell>
                  <div className="flex gap-2">
                    <RoleForm role={role}>
                      <Button variant="outline">Edit</Button>
                    </RoleForm>
                    <Button variant="destructive" onClick={() => router.delete(route('roles.destroy', role.id))}>
                      Delete
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  );
}
