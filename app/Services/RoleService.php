<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RoleService extends BaseService
{
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
}
