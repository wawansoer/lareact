<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_bulk_destroy_tenants()
    {
        $tenants = Tenant::factory()->count(3)->create();
        $tenantIds = $tenants->pluck('id')->toArray();

        $response = $this->post(route('tenants.bulk-delete'), [
            'ids' => $tenantIds,
        ]);

        $response->assertRedirect(route('users.index', ['tab' => 'tenants']));
        $response->assertSessionHas('success', 'Selected tenants deleted successfully.');

        foreach ($tenantIds as $id) {
            $this->assertDatabaseMissing('tenants', ['id' => $id]);
        }
    }

    public function test_can_create_a_tenant()
    {
        $this->post(route('tenants.store'), [
            'name' => 'Test Tenant',
        ])->assertRedirect(route('users.index', ['tab' => 'tenants']));

        $this->assertDatabaseHas('tenants', ['name' => 'Test Tenant']);
    }

    public function test_can_update_a_tenant()
    {
        $tenant = \App\Models\Tenant::factory()->create();
        $this->put(route('tenants.update', $tenant), [
            'name' => 'Updated Name',
        ])->assertRedirect(route('users.index', ['tab' => 'tenants']));

        $this->assertDatabaseHas('tenants', ['id' => $tenant->id, 'name' => 'Updated Name']);
    }

    public function test_can_delete_a_tenant()
    {
        $tenant = \App\Models\Tenant::factory()->create();
        $this->delete(route('tenants.destroy', $tenant))
            ->assertRedirect(route('users.index', ['tab' => 'tenants']));

        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }
}
