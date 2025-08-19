import { Head, useForm } from '@inertiajs/react';
import { Grid3X3Icon, ListIcon, PlusIcon, SearchIcon } from 'lucide-react';
import { FormEventHandler, useState } from 'react';
import { toast } from 'sonner';

import InputError from '@/components/input-error';
import DeleteConfirmationDialog from '@/components/tenants/DeleteConfirmationDialog';
import TenantBulkActions from '@/components/tenants/TenantBulkActions';
import TenantTable from '@/components/tenants/TenantTable';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Textarea } from '@/components/ui/textarea';
import { useTenantSelection } from '@/hooks/tenants/use-tenant-selection';
import { useTenantTableState } from '@/hooks/tenants/use-tenant-table';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Tenants',
    href: '/tenants',
  },
];

interface Tenant {
  id: string;
  name: string;
  description: string;
}

interface Props {
  tenants: {
    data: Tenant[];
    links: Record<string, string>;
    meta: Record<string, unknown>;
  };
}

export default function Tenants({ tenants }: Props) {
  const [open, setOpen] = useState(false);
  const [editingTenant, setEditingTenant] = useState<Tenant | null>(null);
  const [viewMode, setViewMode] = useState<'table' | 'grid'>('table');
  const { globalFilter, setGlobalFilter } = useTenantTableState();
  const { selectedTenants, clearSelection, handleSelectTenant, handleSelectAll, selectAll, setSelectedTenants, setSelectAll } = useTenantSelection(
    tenants.data,
  );
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const [tenantToDelete, setTenantToDelete] = useState<Tenant | null>(null);
  const [bulkDeleteDialogOpen, setBulkDeleteDialogOpen] = useState(false);
  const [tenantsToDeleteBulk, setTenantsToDeleteBulk] = useState<string[]>([]);

  const {
    data,
    setData,
    post,
    put,
    delete: destroy,
    processing,
    errors,
    reset,
  } = useForm({
    name: '',
    description: '',
  });

  const {
    setData: setBulkData,
    post: bulkPost,
    processing: bulkProcessing,
    reset: resetBulk,
  } = useForm({
    ids: [] as string[],
  });

  const openCreateDialog = () => {
    reset();
    setEditingTenant(null);
    setOpen(true);
  };

  const openEditDialog = (tenant: Tenant) => {
    setData({
      name: tenant.name,
      description: tenant.description,
    });
    setEditingTenant(tenant);
    setOpen(true);
  };

  const closeDialog = () => {
    setOpen(false);
    reset();
    setEditingTenant(null);
  };

  const handleBulkDelete = () => {
    if (selectedTenants.length === 0) return;
    setTenantsToDeleteBulk(selectedTenants);
    setBulkDeleteDialogOpen(true);
  };

  const handleConfirmBulkDelete = () => {
    setBulkData('ids', tenantsToDeleteBulk);
    bulkPost(route('tenants.bulk-delete'), {
      onSuccess: () => {
        resetBulk();
        toast.success(`${tenantsToDeleteBulk.length} tenant(s) deleted successfully`);
        clearSelection();
        setSelectedTenants([]);
        setSelectAll(false);
      },
      onError: () => {
        toast.error('Failed to delete selected tenants');
      },
      onFinish: () => {
        setBulkDeleteDialogOpen(false);
        setTenantsToDeleteBulk([]);
      },
    });
  };

  const submit: FormEventHandler = (e) => {
    e.preventDefault();

    if (editingTenant) {
      put(route('tenants.update', editingTenant.id), {
        onSuccess: () => {
          closeDialog();
          toast.success('Tenant updated successfully');
        },
        onError: (errors) => {
          toast.error('Failed to update tenant', {
            description: Object.values(errors).join(', '),
          });
        },
      });
    } else {
      post(route('tenants.store'), {
        onSuccess: () => {
          closeDialog();
          toast.success('Tenant created successfully');
        },
        onError: (errors) => {
          toast.error('Failed to create tenant', {
            description: Object.values(errors).join(', '),
          });
        },
      });
    }
  };

  const deleteTenant = (tenant: Tenant) => {
    setTenantToDelete(tenant);
    setDeleteDialogOpen(true);
  };

  const handleConfirmDelete = () => {
    if (tenantToDelete) {
      destroy(route('tenants.destroy', tenantToDelete.id), {
        onSuccess: () => {
          toast.success('Tenant deleted successfully');
        },
        onError: () => {
          toast.error('Failed to delete tenant');
        },
        onFinish: () => {
          setDeleteDialogOpen(false);
          setTenantToDelete(null);
        },
      });
    }
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Tenants" />

      <Card className="m-4">
        <CardHeader className="gap-8">
          <CardTitle>Tenants</CardTitle>
          <CardDescription>
            <div className="flex flex-col gap-4">
              <div className="flex flex-wrap items-center justify-between gap-2">
                <div className="relative">
                  <SearchIcon className="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground" />
                  <Input
                    placeholder="Search tenants..."
                    value={globalFilter ?? ''}
                    onChange={(event) => setGlobalFilter(event.target.value)}
                    className="w-full pl-8 sm:w-64"
                  />
                </div>
                <div className="flex flex-wrap gap-2">
                  <Tabs value={viewMode} onValueChange={(value) => setViewMode(value as 'table' | 'grid')}>
                    <TabsList>
                      <TabsTrigger value="table" className="flex items-center gap-1">
                        <ListIcon className="h-4 w-4" />
                        <div className="hidden sm:block">Table</div>
                      </TabsTrigger>
                      <TabsTrigger value="grid" className="flex items-center gap-1">
                        <Grid3X3Icon className="h-4 w-4" />
                        <div className="hidden sm:block"> Grid </div>
                      </TabsTrigger>
                    </TabsList>
                  </Tabs>
                  <Dialog
                    open={open}
                    onOpenChange={(isOpen) => {
                      setOpen(isOpen);
                      if (!isOpen) {
                        reset();
                        setEditingTenant(null);
                      }
                    }}
                  >
                    <DialogTrigger asChild>
                      <Button onClick={openCreateDialog}>
                        <PlusIcon className="mr-2 h-4 w-4" />
                        <div className="hidden sm:block"> Add </div> Tenant
                      </Button>
                    </DialogTrigger>
                    <DialogContent className="sm:max-w-[425px]">
                      <DialogHeader>
                        <DialogTitle>{editingTenant ? 'Edit Tenant' : 'Create Tenant'}</DialogTitle>
                        <DialogDescription>{editingTenant ? 'Edit the tenant details.' : 'Enter the details for the new tenant.'}</DialogDescription>
                      </DialogHeader>
                      <form onSubmit={submit}>
                        <div className="grid gap-4 py-4">
                          <div className="grid grid-cols-1 items-start gap-2">
                            <Label htmlFor="name" className="text-left">
                              Name
                            </Label>
                            <div className="col-span-1">
                              <Input
                                id="name"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                className={errors.name ? 'border-red-500' : ''}
                              />
                              <InputError message={errors.name} />
                            </div>
                          </div>
                          <div className="grid grid-cols-1 items-start gap-2">
                            <Label htmlFor="description" className="text-left">
                              Description
                            </Label>
                            <div className="col-span-1">
                              <Textarea
                                id="description"
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                                className={errors.description ? 'border-red-500' : ''}
                              />
                              <InputError message={errors.description} />
                            </div>
                          </div>
                        </div>
                        <DialogFooter>
                          <Button type="button" variant="outline" onClick={closeDialog}>
                            Cancel
                          </Button>
                          <Button type="submit" disabled={processing}>
                            {processing ? 'Saving...' : editingTenant ? 'Update Tenant' : 'Create Tenant'}
                          </Button>
                        </DialogFooter>
                      </form>
                    </DialogContent>
                  </Dialog>
                </div>
              </div>
              <TenantBulkActions selectedTenantsCount={selectedTenants.length} onBulkDelete={handleBulkDelete} onClearSelection={clearSelection} />
            </div>
          </CardDescription>
        </CardHeader>
        <CardContent>
          <TenantTable
            data={tenants.data}
            openEditDialog={openEditDialog}
            deleteTenant={deleteTenant}
            viewMode={viewMode}
            selectedTenants={selectedTenants}
            selectAll={selectAll}
            handleSelectTenant={handleSelectTenant}
            handleSelectAll={handleSelectAll}
          />
        </CardContent>
      </Card>
      <DeleteConfirmationDialog
        open={deleteDialogOpen}
        onOpenChange={setDeleteDialogOpen}
        tenant={tenantToDelete || undefined}
        onConfirm={handleConfirmDelete}
        isProcessing={processing}
      />

      <DeleteConfirmationDialog
        open={bulkDeleteDialogOpen}
        onOpenChange={setBulkDeleteDialogOpen}
        tenant={tenantsToDeleteBulk.length > 0 ? { id: 'bulk', name: `${tenantsToDeleteBulk.length} selected tenants` } : undefined}
        onConfirm={handleConfirmBulkDelete}
        isProcessing={bulkProcessing}
      />
    </AppLayout>
  );
}
