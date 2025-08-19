import { useState } from 'react';

interface Tenant {
  id: string;
}

export function useTenantSelection(tenants: Tenant[]) {
  const [selectedTenants, setSelectedTenants] = useState<string[]>([]);
  const [selectAll, setSelectAll] = useState(false);

  const handleSelectTenant = (tenantId: string, checked: boolean) => {
    if (checked) {
      setSelectedTenants((prev) => [...prev, tenantId]);
    } else {
      setSelectedTenants((prev) => prev.filter((id) => id !== tenantId));
    }
  };

  const handleSelectAll = (checked: boolean) => {
    setSelectAll(checked);
    if (checked) {
      setSelectedTenants(tenants.map((tenant) => tenant.id));
    } else {
      setSelectedTenants([]);
    }
  };

  const clearSelection = () => {
    setSelectedTenants([]);
    setSelectAll(false);
  };

  return {
    selectedTenants,
    selectAll,
    handleSelectTenant,
    handleSelectAll,
    clearSelection,
    setSelectedTenants, // Expose for external use if needed (e.g., after bulk delete)
    setSelectAll, // Expose for external use if needed
  };
}
