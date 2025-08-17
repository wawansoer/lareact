<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TenantService extends BaseService
{
    /**
     * Get the model instance.
     */
    public function getModel(): Model
    {
        return new Tenant;
    }

    /**
     * Get a paginated list of tenants.
     */
    public function getPaginatedTenants(Request $request): LengthAwarePaginator
    {
        return $this->getPaginatedData($request);
    }

    /**
     * Get all tenants.
     */
    public function getAllTenants()
    {
        return $this->model->query()->select('id', 'name')->get();
    }

    /**
     * Bulk delete tenants.
     *
     * @param  array<int, string>  $ids
     */
    public function bulkDeleteTenants(array $ids): int
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * Delete a tenant.
     *
     * @throws \Exception
     */
    public function deleteTenant(Tenant $tenant): ?bool
    {
        return $tenant->delete();
    }
}
