<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\BulkDestroyRoleRequest;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        Role::create($request->validated());

        return redirect()
            ->route('users.index', ['tab' => 'roles'])
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update($request->validated());

        return redirect()
            ->route('users.index', ['tab' => 'roles'])
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $this->roleService->deleteRole($role);

        return redirect()
            ->route('users.index', ['tab' => 'roles'])
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Remove multiple specified resources from storage.
     */
    public function bulkDestroy(BulkDestroyRoleRequest $request): RedirectResponse
    {
        $this->roleService->bulkDeleteRoles($request->validated('ids'));

        return redirect()
            ->route('users.index', ['tab' => 'roles'])
            ->with('success', 'Selected roles deleted successfully.');
    }
}
