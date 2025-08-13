import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Paginated, Permission } from '@/types';
import { router } from '@inertiajs/react';
import { PermissionForm } from '../forms/permission-form';

export function PermissionsTable({ permissions }: { permissions: Paginated<Permission> }) {
  return (
    <Card>
      <CardHeader>
        <div className="flex justify-between">
          <CardTitle>Permissions</CardTitle>
          <PermissionForm>
            <Button>Create Permission</Button>
          </PermissionForm>
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
            {permissions.data.map((permission) => (
              <TableRow key={permission.id}>
                <TableCell>{permission.name}</TableCell>
                <TableCell>
                  <div className="flex gap-2">
                    <PermissionForm permission={permission}>
                      <Button variant="outline">Edit</Button>
                    </PermissionForm>
                    <Button variant="destructive" onClick={() => router.delete(route('permissions.destroy', permission.id))}>
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
