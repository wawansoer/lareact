<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\BulkDestroyPermissionRequest;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
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
    public function store(StorePermissionRequest $request): RedirectResponse
    {
        Permission::create($request->validated());

        return redirect()
            ->route('users.index', ['tab' => 'permissions'])
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $permission->update($request->validated());

        return redirect()
            ->route('users.index', ['tab' => 'permissions'])
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        $this->permissionService->deletePermission($permission);

        return redirect()
            ->route('users.index', ['tab' => 'permissions'])
            ->with('success', 'Permission deleted successfully.');
    }

    /**
     * Bulk delete the specified resources from storage.
     */
    public function bulkDestroy(BulkDestroyPermissionRequest $request): RedirectResponse
    {
        $this->permissionService->bulkDeletePermissions($request->validated('ids'));

        return redirect()
            ->route('users.index', ['tab' => 'permissions'])
            ->with('success', 'Selected permissions deleted successfully.');
    }
}
