<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tenant\BulkDestroyTenantRequest;
use App\Http\Requests\Tenant\StoreTenantRequest;
use App\Http\Requests\Tenant\UpdateTenantRequest;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\RedirectResponse;

class TenantController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
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
    public function store(StoreTenantRequest $request): RedirectResponse
    {
        Tenant::create($request->validated());

        return redirect()
            ->route('users.index', ['tab' => 'tenants'])
            ->with('success', 'Tenant created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $tenant->update($request->validated());

        return redirect()
            ->route('users.index', ['tab' => 'tenants'])
            ->with('success', 'Tenant updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $this->tenantService->deleteTenant($tenant);

        return redirect()
            ->route('users.index', ['tab' => 'tenants'])
            ->with('success', 'Tenant deleted successfully.');
    }

    /**
     * Remove multiple specified resources from storage.
     */
    public function bulkDestroy(BulkDestroyTenantRequest $request): RedirectResponse
    {
        $this->tenantService->bulkDeleteTenants($request->validated('ids'));

        return redirect()
            ->route('users.index', ['tab' => 'tenants'])
            ->with('success', 'Selected tenants deleted successfully.');
    }
}
