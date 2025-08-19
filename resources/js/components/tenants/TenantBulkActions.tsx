import { Button } from '@/components/ui/button';
import { TrashIcon } from 'lucide-react';

interface TenantBulkActionsProps {
  selectedTenantsCount: number;
  onBulkDelete: () => void;
  onClearSelection: () => void;
}

export default function TenantBulkActions({ selectedTenantsCount, onBulkDelete, onClearSelection }: TenantBulkActionsProps) {
  if (selectedTenantsCount === 0) {
    return null;
  }

  return (
    <div className="flex max-w-fit flex-wrap items-center gap-2 rounded-md bg-blue-100 p-2">
      <span className="text-sm text-blue-700">
        {selectedTenantsCount} {selectedTenantsCount === 1 ? 'tenant' : 'tenants'} selected
      </span>
      <Button variant="destructive" size="sm" onClick={onBulkDelete}>
        <TrashIcon className="mr-1 h-4 w-4" />
        Delete Selected
      </Button>
      <Button variant="secondary" size="sm" onClick={onClearSelection}>
        Cancel
      </Button>
    </div>
  );
}
