import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Paginated, Tenant } from '@/types';
import { router } from '@inertiajs/react';
import { TenantForm } from '../forms/tenant-form';

export function TenantsTable({ tenants }: { tenants: Paginated<Tenant> }) {
  return (
    <Card>
      <CardHeader>
        <div className="flex justify-between">
          <CardTitle>Tenants</CardTitle>
          <TenantForm>
            <Button>Create Tenant</Button>
          </TenantForm>
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
            {tenants.data.map((tenant) => (
              <TableRow key={tenant.id}>
                <TableCell>{tenant.name}</TableCell>
                <TableCell>
                  <div className="flex gap-2">
                    <TenantForm tenant={tenant}>
                      <Button variant="outline">Edit</Button>
                    </TenantForm>
                    <Button variant="destructive" onClick={() => router.delete(route('tenants.destroy', tenant.id))}>
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
