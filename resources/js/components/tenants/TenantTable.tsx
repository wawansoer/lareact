import {
  ColumnDef,
  flexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useReactTable,
} from '@tanstack/react-table';
import { PencilIcon, TrashIcon } from 'lucide-react';
import { useMemo } from 'react';

import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useTenantTableState } from '@/hooks/tenants/use-tenant-table';
import TenantCard from './TenantCard';

interface Tenant {
  id: string;
  name: string;
  description: string;
}

interface TenantTableProps {
  data: Tenant[];
  openEditDialog: (tenant: Tenant) => void;
  deleteTenant: (tenant: Tenant) => void;
  viewMode: 'table' | 'grid';
  selectedTenants: string[];
  selectAll: boolean;
  handleSelectTenant: (tenantId: string, checked: boolean) => void;
  handleSelectAll: (checked: boolean) => void;
}

export default function TenantTable({
  data,
  openEditDialog,
  deleteTenant,
  viewMode,
  selectedTenants,
  selectAll,
  handleSelectTenant,
  handleSelectAll,
}: TenantTableProps) {
  const { sorting, setSorting, globalFilter, setGlobalFilter, pagination, setPagination } = useTenantTableState();
  // The useTenantSelection hook is no longer needed here as selection state is passed down from parent
  // const { selectedTenants, selectAll, handleSelectTenant, handleSelectAll } = useTenantSelection(data);

  // Define columns for tanstack table
  const columns = useMemo<ColumnDef<Tenant>[]>(
    () => [
      {
        id: 'select',
        header: () => <Checkbox checked={selectAll} onCheckedChange={(checked) => handleSelectAll(checked as boolean)} aria-label="Select all" />,
        cell: ({ row }) => (
          <Checkbox
            checked={selectedTenants.includes(row.original.id)}
            onCheckedChange={(checked) => handleSelectTenant(row.original.id, checked as boolean)}
            aria-label="Select tenant"
          />
        ),
        enableSorting: false,
        enableHiding: false,
      },
      {
        accessorKey: 'name',
        header: ({ column }) => {
          return (
            <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}>
              Name
              {column.getIsSorted() === 'asc' ? ' ↑' : column.getIsSorted() === 'desc' ? ' ↓' : ''}
            </Button>
          );
        },
        cell: ({ row }) => <div className="font-medium">{row.getValue('name')}</div>,
      },
      {
        accessorKey: 'description',
        header: 'Description',
      },
      {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => (
          <div className="flex justify-center gap-2">
            <Button variant="outline" size="icon" onClick={() => openEditDialog(row.original)}>
              <PencilIcon className="h-2 w-2" />
            </Button>
            <Button variant="destructive" size="icon" onClick={() => deleteTenant(row.original)}>
              <TrashIcon className="h-4 w-4" />
            </Button>
          </div>
        ),
      },
    ],
    // Dependencies for useMemo should include props that affect column definitions
    [selectedTenants, selectAll, handleSelectTenant, handleSelectAll, openEditDialog, deleteTenant],
  );

  // Create table instance
  const table = useReactTable({
    data,
    columns,
    state: {
      sorting,
      globalFilter,
      pagination,
    },
    onSortingChange: setSorting,
    onGlobalFilterChange: setGlobalFilter,
    onPaginationChange: setPagination,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
  });

  if (viewMode === 'table') {
    return (
      <div className="rounded-md border">
        <Table>
          <TableHeader>
            {table.getHeaderGroups().map((headerGroup) => (
              <TableRow key={headerGroup.id}>
                {headerGroup.headers.map((header) => (
                  <TableHead key={header.id} className={header.id === 'actions' ? 'text-center' : ''}>
                    {header.isPlaceholder ? null : flexRender(header.column.columnDef.header, header.getContext())}
                  </TableHead>
                ))}
              </TableRow>
            ))}
          </TableHeader>
          <TableBody>
            {table.getRowModel().rows?.length ? (
              table.getRowModel().rows.map((row) => (
                <TableRow key={row.id} data-state={row.getIsSelected() && 'selected'}>
                  {row.getVisibleCells().map((cell) => (
                    <TableCell key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</TableCell>
                  ))}
                </TableRow>
              ))
            ) : (
              <TableRow>
                <TableCell colSpan={columns.length} className="h-24 text-center">
                  No tenants found.
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </div>
    );
  } else {
    // Grid view
    return (
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {data.length > 0 ? (
          data.map((tenant) => (
            <TenantCard
              key={tenant.id}
              tenant={tenant}
              isSelected={selectedTenants.includes(tenant.id)}
              onToggleSelect={handleSelectTenant}
              onEdit={openEditDialog}
              onDelete={deleteTenant}
            />
          ))
        ) : (
          <div className="col-span-full py-8 text-center">
            <p>No tenants found.</p>
          </div>
        )}
      </div>
    );
  }
}
