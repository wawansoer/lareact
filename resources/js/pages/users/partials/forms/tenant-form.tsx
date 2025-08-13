import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Tenant } from '@/types';
import { useForm } from '@inertiajs/react';

export function TenantForm({ tenant, children }: { tenant?: Tenant; children: React.ReactNode }) {
  const { data, setData, post, put, processing, errors, reset } = useForm({
    name: tenant?.name || '',
  });

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    if (tenant) {
      put(route('tenants.update', tenant.id), {
        onSuccess: () => reset(),
      });
    } else {
      post(route('tenants.store'), {
        onSuccess: () => reset(),
      });
    }
  };

  return (
    <Dialog>
      <DialogTrigger asChild>{children}</DialogTrigger>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>{tenant ? 'Edit' : 'Create'} Tenant</DialogTitle>
          <DialogDescription>{tenant ? 'Edit the details of the tenant.' : 'Enter the details of the new tenant.'}</DialogDescription>
        </DialogHeader>
        <form onSubmit={submit} className="space-y-4">
          <div>
            <Label htmlFor="name">Name</Label>
            <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} />
            <InputError message={errors.name} />
          </div>
          <Button type="submit" disabled={processing}>
            {tenant ? 'Update' : 'Create'}
          </Button>
        </form>
      </DialogContent>
    </Dialog>
  );
}
