import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Permission } from '@/types';
import { useForm } from '@inertiajs/react';

export function PermissionForm({ permission, children }: { permission?: Permission; children: React.ReactNode }) {
  const { data, setData, post, put, processing, errors, reset } = useForm({
    name: permission?.name || '',
  });

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    if (permission) {
      put(route('permissions.update', permission.id), {
        onSuccess: () => reset(),
      });
    } else {
      post(route('permissions.store'), {
        onSuccess: () => reset(),
      });
    }
  };

  return (
    <Dialog>
      <DialogTrigger asChild>{children}</DialogTrigger>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>{permission ? 'Edit' : 'Create'} Permission</DialogTitle>
          <DialogDescription>{permission ? 'Edit the details of the permission.' : 'Enter the details of the new permission.'}</DialogDescription>
        </DialogHeader>
        <form onSubmit={submit} className="space-y-4">
          <div>
            <Label htmlFor="name">Name</Label>
            <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} />
            <InputError message={errors.name} />
          </div>
          <div className="flex justify-end">
            <Button type="submit" disabled={processing}>
              {permission ? 'Update' : 'Create'}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
}
