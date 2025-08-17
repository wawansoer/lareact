<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\BulkDestroyUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\PermissionService;
use App\Services\RoleService;
use App\Services\TenantService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * UserController handles all user-related operations including CRUD operations,
 * user management, and coordination with related services.
 */
class UserController extends Controller
{
    protected $userService;

    protected $tenantService;

    protected $roleService;

    protected $permissionService;

    /**
     * UserController constructor.
     */
    public function __construct(
        UserService $userService,
        TenantService $tenantService,
        RoleService $roleService,
        PermissionService $permissionService
    ) {
        // Inject service dependencies
        $this->userService = $userService;
        $this->tenantService = $tenantService;
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

    /**
     * Display a listing of the resource.
     *
     * This method optimizes data loading by:
     * 1. Eagerly loading the active tab data
     * 2. Lazy loading inactive tab data to reduce initial payload
     * 3. Always loading all tenants for dropdowns and filters
     */
    public function index(Request $request): Response
    {
        $tab = $request->input('tab', 'users');

        // Prepare data with optimized loading strategy
        $props = [
            'users' => $tab === 'users'
                ? $this->userService->getPaginatedUsers($request)
                : Inertia::lazy(fn () => $this->userService->getPaginatedUsers($request)),
            'tenants' => $tab === 'tenants'
                ? $this->tenantService->getPaginatedTenants($request)
                : Inertia::lazy(fn () => $this->tenantService->getPaginatedTenants($request)),
            'roles' => $tab === 'roles'
                ? $this->roleService->getPaginatedRoles($request)
                : Inertia::lazy(fn () => $this->roleService->getPaginatedRoles($request)),
            'permissions' => $tab === 'permissions'
                ? $this->permissionService->getPaginatedPermissions($request)
                : Inertia::lazy(fn () => $this->permissionService->getPaginatedPermissions($request)),
        ];

        return Inertia::render('users/index', $props);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return redirect()
                ->route('users.index', ['tab' => 'users'])
                ->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            \Log::error('Error creating user: '.$e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create user. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $this->userService->updateUser($user, $request->validated());

            return redirect()
                ->route('users.index', ['tab' => 'users'])
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating user: '.$e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update user. Please try again.');
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->userService->deleteUser($user);

            return redirect()
                ->route('users.index', ['tab' => 'users'])
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting user: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Remove multiple specified resources from storage.
     */
    public function bulkDestroy(BulkDestroyUserRequest $request): RedirectResponse
    {
        $this->userService->bulkDeleteUsers($request->validated('ids'));

        return redirect()->route('users.index', ['tab' => 'users'])->with('success', 'Selected users deleted successfully.');
    }
}
