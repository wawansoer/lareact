import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Paginated, User } from '@/types';
import { router } from '@inertiajs/react';
import { UserForm } from '../forms/user-form';

export function UsersTable({ users }: { users: Paginated<User> }) {
  return (
    <Card>
      <CardHeader>
        <div className="flex justify-between">
          <CardTitle>Users</CardTitle>
          <UserForm>
            <Button>Create User</Button>
          </UserForm>
        </div>
      </CardHeader>
      <CardContent>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Name</TableHead>
              <TableHead>Email</TableHead>
              <TableHead />
            </TableRow>
          </TableHeader>
          <TableBody>
            {users.data.map((user) => (
              <TableRow key={user.id}>
                <TableCell>{user.name}</TableCell>
                <TableCell>{user.email}</TableCell>
                <TableCell>
                  <div className="flex gap-2">
                    <UserForm user={user}>
                      <Button variant="outline">Edit</Button>
                    </UserForm>
                    <Button variant="destructive" onClick={() => router.delete(route('users.destroy', user.id))}>
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
