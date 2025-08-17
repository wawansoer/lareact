<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PermissionService extends BaseService
{
    /**
     * The columns to be searched in a global search.
     *
     * @var array<int, string>
     */
    protected array $globalSearchColumns = ['name', 'tenant.name'];

    /**
     * The relationships to be eager loaded.
     *
     * @var array<int, string>
     */
    protected array $relationships = ['tenant'];

    /**
     * Get the model instance.
     */
    public function getModel(): Model
    {
        return new Permission;
    }

    /**
     * Get a paginated list of permissions.
     */
    public function getPaginatedPermissions(Request $request): LengthAwarePaginator
    {
        return $this->getPaginatedData($request);
    }

    /**
     * Bulk delete permissions by their IDs.
     *
     * @param  array<int, int>  $ids
     * @return int The number of deleted permissions.
     */
    public function bulkDeletePermissions(array $ids): int
    {
        return Permission::whereIn('id', $ids)->delete();
    }
}
