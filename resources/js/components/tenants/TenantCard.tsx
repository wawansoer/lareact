import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { PencilIcon, TrashIcon } from 'lucide-react';

interface Tenant {
  id: string;
  name: string;
  description: string;
}

interface TenantCardProps {
  tenant: Tenant;
  isSelected: boolean;
  onToggleSelect: (id: string, checked: boolean) => void;
  onEdit: (tenant: Tenant) => void;
  onDelete: (tenant: Tenant) => void;
}

export default function TenantCard({ tenant, isSelected, onToggleSelect, onEdit, onDelete }: TenantCardProps) {
  return (
    <Card className="flex flex-col">
      <CardContent className="flex-1 p-4">
        <div className="mb-2 flex items-start justify-between">
          <Checkbox checked={isSelected} onCheckedChange={(checked) => onToggleSelect(tenant.id, checked as boolean)} aria-label="Select tenant" />
        </div>
        <h3 className="mb-1 text-lg font-semibold">{tenant.name}</h3>
        <p className="mb-4 text-sm text-muted-foreground">{tenant.description || 'No description provided'}</p>
      </CardContent>
      <div className="flex justify-end gap-2 p-4 pt-0">
        <Button variant="outline" size="sm" onClick={() => onEdit(tenant)}>
          <PencilIcon className="h-4 w-4" />
        </Button>
        <Button variant="outline" size="sm" onClick={() => onDelete(tenant)}>
          <TrashIcon className="h-4 w-4" />
        </Button>
      </div>
    </Card>
  );
}
