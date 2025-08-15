<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserService extends BaseService
{
    /**
     * The columns to be searched in a global search.
     *
     * @var array<int, string>
     */
    protected array $globalSearchColumns = ['name', 'email'];

    /**
     * Get the model instance.
     */
    public function getModel(): Model
    {
        return new User;
    }

    /**
     * Get a paginated list of users.
     */
    public function getPaginatedUsers(Request $request): LengthAwarePaginator
    {
        return $this->getPaginatedData($request);
    }
}
