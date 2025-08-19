<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RoleService extends BaseService
{
    /**
     * The columns to be searched in a global search.
     *
     * @var array<int, string>
     */
    protected array $globalSearchColumns = ['name'];

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
        return new Role;
    }

    /**
     * Get a paginated list of roles.
     */
    public function getPaginatedRoles(Request $request): LengthAwarePaginator
    {
        return $this->getPaginatedData($request);
    }

    /**
     * Bulk delete roles.
     *
     * @param  array<int, int>  $ids
     */
    public function bulkDeleteRoles(array $ids): int
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * Delete a role.
     *
     * @throws \Exception
     */
    public function deleteRole(Role $role): ?bool
    {
        return $role->delete();
    }
}
