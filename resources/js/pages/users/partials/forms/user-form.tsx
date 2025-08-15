import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input, PasswordInput } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { User } from '@/types';
import { useForm } from '@inertiajs/react';

export function UserForm({ user, children }: { user?: User; children: React.ReactNode }) {
  const { data, setData, post, put, processing, errors, reset } = useForm({
    name: user?.name || '',
    email: user?.email || '',
    password: '',
    password_confirmation: '',
  });

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    if (user) {
      put(route('users.update', user.id), {
        onSuccess: () => reset(),
      });
    } else {
      post(route('users.store'), {
        onSuccess: () => reset(),
      });
    }
  };

  return (
    <Dialog>
      <DialogTrigger asChild>{children}</DialogTrigger>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>{user ? 'Edit' : 'Create'} User</DialogTitle>
          <DialogDescription>{user ? 'Edit the details of the user.' : 'Enter the details of the new user.'}</DialogDescription>
        </DialogHeader>
        <form onSubmit={submit} className="space-y-4">
          <div>
            <Label htmlFor="name">Name</Label>
            <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} />
            <InputError message={errors.name} />
          </div>
          <div>
            <Label htmlFor="email">Email</Label>
            <Input id="email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} />
            <InputError message={errors.email} />
          </div>
          <div>
            <Label htmlFor="password">Password</Label>
            <PasswordInput id="password" type="password" value={data.password} onChange={(e) => setData('password', e.target.value)} />
            <InputError message={errors.password} />
          </div>
          <div>
            <Label htmlFor="password_confirmation">Confirm Password</Label>
            <PasswordInput
              id="password_confirmation"
              type="password"
              value={data.password_confirmation}
              onChange={(e) => setData('password_confirmation', e.target.value)}
            />
            <InputError message={errors.password_confirmation} />
          </div>
          <div className="flex justify-end">
            <Button type="submit" disabled={processing}>
              {user ? 'Update' : 'Create'}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
}
