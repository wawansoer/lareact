<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    /**
     * Create a new user.
     *
     * @throws \Exception
     */
    public function createUser(array $data): User
    {
        return $this->model->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Update an existing user.
     *
     * @throws \Exception
     */
    public function updateUser(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    /**
     * Delete a user.
     *
     * @throws \Exception
     */
    public function deleteUser(User $user): ?bool
    {
        return $user->delete();
    }
}
