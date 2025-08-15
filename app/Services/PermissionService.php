<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PermissionService extends BaseService
{
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
}
