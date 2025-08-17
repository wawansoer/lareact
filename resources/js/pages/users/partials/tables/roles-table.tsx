import { DataTablePagination } from '@/components/data-table-pagination';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Paginated, Role } from '@/types';
import { router, usePage } from '@inertiajs/react';
import {
  ColumnDef,
  ColumnFiltersState,
  flexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  PaginationState,
  SortingState,
  useReactTable,
  VisibilityState,
} from '@tanstack/react-table';
import { ArrowDownAZ, ArrowUpZA, Eye, MoreVertical, Pencil, Trash2, X } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { RoleForm } from '../forms/role-form';

const roleColumns: ColumnDef<Role>[] = [
  {
    id: 'select',
    header: ({ table }) => (
      <Checkbox
        checked={table.getIsAllPageRowsSelected()}
        onCheckedChange={(value) => table.toggleAllPageRowsSelected(!!value)}
        aria-label="Select all"
      />
    ),
    cell: ({ row }) => <Checkbox checked={row.getIsSelected()} onCheckedChange={(value) => row.toggleSelected(!!value)} aria-label="Select row" />,
    enableSorting: false,
    enableHiding: false,
  },
  {
    accessorKey: 'name',
    header: ({ column }) => {
      return (
        <div className="my-2 flex cursor-pointer items-center justify-between" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}>
          <span>Name</span>
          {{
            asc: <ArrowUpZA className="h-4 w-4" />,
            desc: <ArrowDownAZ className="h-4 w-4" />,
          }[column.getIsSorted() as string] ?? null}
        </div>
      );
    },
    cell: ({ row }) => <div className="capitalize">{row.getValue('name')}</div>,
    enableSorting: true,
    enableColumnFilter: true,
  },
  {
    id: 'tenant',
    header: 'Tenant',
    cell: ({ row }) => {
      const tenantName = row.original?.tenant?.name || '';
      return <div className="capitalize">{tenantName}</div>;
    },
    enableSorting: true,
    enableColumnFilter: true,
  },
  {
    id: 'actions',
    header: 'Actions',
    cell: ({ row }) => {
      const role = row.original;

      const handleDelete = () => {
        router.delete(route('roles.destroy', role.id), {
          onError: (error) => {
            console.error('Error deleting role:', error);
            alert('Failed to delete role. Please try again.');
          },
        });
      };

      return (
        <Popover>
          <PopoverTrigger asChild>
            <Button variant="ghost" size="sm">
              <MoreVertical className="h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent className="w-40 p-2">
            <div className="flex flex-col gap-2">
              <RoleForm role={role}>
                <Button variant="ghost" className="w-full justify-start">
                  <Pencil className="mr-2 h-4 w-4" />
                  Edit
                </Button>
              </RoleForm>
              <AlertDialog>
                <AlertDialogTrigger asChild>
                  <Button variant="ghost" className="w-full justify-start text-red-600">
                    <Trash2 className="mr-2 h-4 w-4" />
                    Delete
                  </Button>
                </AlertDialogTrigger>
                <AlertDialogContent>
                  <AlertDialogHeader>
                    <AlertDialogTitle>Delete Role</AlertDialogTitle>
                    <AlertDialogDescription>Are you sure you want to delete this role? This action cannot be undone.</AlertDialogDescription>
                  </AlertDialogHeader>
                  <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction variant="destructive" onClick={handleDelete}>
                      Delete
                    </AlertDialogAction>
                  </AlertDialogFooter>
                </AlertDialogContent>
              </AlertDialog>
            </div>
          </PopoverContent>
        </Popover>
      );
    },
    enableHiding: false,
  },
];

interface RolesTableProps {
  roles: Paginated<Role>;
}

export function RolesTable({ roles }: RolesTableProps) {
  const { url } = usePage();
  const data = useMemo(() => roles.data, [roles]);
  const totalRows = useMemo(() => roles.total, [roles]);

  const [sorting, setSorting] = useState<SortingState>([]);
  const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([]);
  const [globalFilter, setGlobalFilter] = useState('');
  const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({});
  const [rowSelection, setRowSelection] = useState<Record<string, boolean>>({});
  const [pagination, setPagination] = useState<PaginationState>({
    pageIndex: roles.current_page - 1,
    pageSize: roles.per_page,
  });

  const [isLoading, setIsLoading] = useState(false);

  const table = useReactTable({
    data,
    columns: roleColumns,
    pageCount: Math.ceil(totalRows / pagination.pageSize),
    state: {
      sorting,
      columnFilters,
      globalFilter,
      columnVisibility,
      rowSelection,
      pagination,
    },
    onSortingChange: setSorting,
    onColumnFiltersChange: setColumnFilters,
    onGlobalFilterChange: setGlobalFilter,
    onColumnVisibilityChange: setColumnVisibility,
    onRowSelectionChange: setRowSelection,
    onPaginationChange: setPagination,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getRowId: (row) => String(row.id),
    enableRowSelection: true,
    manualPagination: true,
    manualFiltering: true,
    manualSorting: true,
  });

  useEffect(() => {
    setIsLoading(true);
    const params = {
      page: pagination.pageIndex + 1,
      per_page: pagination.pageSize,
      sort: sorting.length ? `${sorting[0].id},${sorting[0].desc ? 'desc' : 'asc'}` : undefined,
      ...columnFilters.reduce((obj, filter) => ({ ...obj, [filter.id]: filter.value }), {}),
      global: globalFilter || undefined,
    };

    router.get(url, params, {
      preserveState: true,
      replace: true,
      onFinish: () => setIsLoading(false),
    });
  }, [pagination, sorting, columnFilters, globalFilter, url]);

  // Handle bulk delete operation
  const handleBulkDelete = () => {
    const selectedRoleIds = table.getSelectedRowModel().flatRows.map((row) => row.original.id);
    if (selectedRoleIds.length === 0) return;

    // Perform bulk delete
    router.post(
      route('roles.bulk-delete'),
      { ids: selectedRoleIds },
      {
        onSuccess: () => {
          // Clear selection after successful deletion
          table.resetRowSelection();
          // Show success message
          alert(`${selectedRoleIds.length} role(s) deleted successfully.`);
        },
        onError: (error: Record<string, string>) => {
          console.error('Error bulk deleting roles:', error);
          alert('Failed to delete selected roles. Please try again.');
        },
      },
    );
  };

  return (
    <Card>
      <CardHeader>
        <CardTitle>Roles</CardTitle>
        <CardDescription>Manage Roles</CardDescription>
      </CardHeader>
      <CardContent>
        <div className="flex items-center gap-2 py-2">
          <Input
            placeholder="Filter by role name..."
            value={globalFilter ?? ''}
            onChange={(event) => setGlobalFilter(event.target.value)}
            className="max-w-sm"
          />
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="outline" size="icon" className="ml-auto">
                <Eye />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              {table
                .getAllColumns()
                .filter((column) => column.getCanHide())
                .map((column) => {
                  return (
                    <DropdownMenuCheckboxItem
                      key={column.id}
                      className="capitalize"
                      checked={column.getIsVisible()}
                      onCheckedChange={(value) => column.toggleVisibility(!!value)}
                    >
                      {column.id}
                    </DropdownMenuCheckboxItem>
                  );
                })}
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
        <div className="flex flex-wrap items-center justify-between gap-2 py-2">
          <div className="flex items-center gap-4">
            {Object.keys(rowSelection).length > 0 && (
              <div className="flex items-center gap-2">
                <span className="text-sm text-muted-foreground">{Object.keys(rowSelection).length} selected</span>
                <Button variant="outline" size="sm" onClick={() => table.resetRowSelection()}>
                  <X className="mr-1 h-4 w-4" />
                  Clear
                </Button>
              </div>
            )}
          </div>
          <div className="flex items-center gap-2">
            {Object.keys(rowSelection).length > 0 && (
              <AlertDialog>
                <AlertDialogTrigger asChild>
                  <Button variant="destructive" size="sm">
                    <Trash2 className="mr-1 h-4 w-4" />
                    Delete Selected ({Object.keys(rowSelection).length})
                  </Button>
                </AlertDialogTrigger>
                <AlertDialogContent>
                  <AlertDialogHeader>
                    <AlertDialogTitle>Delete Selected Roles</AlertDialogTitle>
                    <AlertDialogDescription>
                      Are you sure you want to delete {Object.keys(rowSelection).length} role(s)? This action cannot be undone.
                    </AlertDialogDescription>
                  </AlertDialogHeader>
                  <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction variant="destructive" onClick={handleBulkDelete}>
                      Delete {Object.keys(rowSelection).length} Role(s)
                    </AlertDialogAction>
                  </AlertDialogFooter>
                </AlertDialogContent>
              </AlertDialog>
            )}
          </div>
        </div>
        <div className="rounded-md border">
          <Table>
            <TableHeader>
              {table.getHeaderGroups().map((headerGroup) => (
                <TableRow key={headerGroup.id}>
                  {headerGroup.headers.map((header) => {
                    return (
                      <TableHead key={header.id} colSpan={header.colSpan}>
                        {header.isPlaceholder ? null : flexRender(header.column.columnDef.header, header.getContext())}
                      </TableHead>
                    );
                  })}
                </TableRow>
              ))}
            </TableHeader>
            <TableBody>
              {isLoading ? (
                <TableRow>
                  <TableCell colSpan={roleColumns.length} className="h-24 text-center">
                    Loading...
                  </TableCell>
                </TableRow>
              ) : table.getRowModel().rows?.length ? (
                table.getRowModel().rows.map((row) => (
                  <TableRow key={row.id} data-state={row.getIsSelected() && 'selected'}>
                    {row.getVisibleCells().map((cell) => (
                      <TableCell key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</TableCell>
                    ))}
                  </TableRow>
                ))
              ) : (
                <TableRow>
                  <TableCell colSpan={roleColumns.length} className="h-24 text-center">
                    No results.
                  </TableCell>
                </TableRow>
              )}
            </TableBody>
          </Table>
        </div>
        <div className="py-4">
          <DataTablePagination table={table} />
        </div>
      </CardContent>
    </Card>
  );
}
