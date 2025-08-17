<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_bulk_destroy_permissions()
    {
        $permissions = collect();
        for ($i = 0; $i < 3; $i++) {
            $permissions->push(Permission::create(['name' => "permission{$i}", 'guard_name' => 'web']));
        }
        $permissionIds = $permissions->pluck('id')->toArray();

        $response = $this->post(route('permissions.bulk-delete'), [
            'ids' => $permissionIds,
        ]);

        $response->assertRedirect(route('users.index', ['tab' => 'permissions']));
        $response->assertSessionHas('success', 'Selected permissions deleted successfully.');

        foreach ($permissionIds as $id) {
            $this->assertDatabaseMissing('permissions', ['id' => $id]);
        }
    }

    public function test_can_create_a_permission()
    {
        $this->post(route('permissions.store'), [
            'name' => 'Test Permission',
        ])->assertRedirect(route('users.index', ['tab' => 'permissions']));

        $this->assertDatabaseHas('permissions', ['name' => 'Test Permission']);
    }

    public function test_can_update_a_permission()
    {
        $permission = Permission::create(['name' => 'Test Permission']);
        $this->put(route('permissions.update', $permission), [
            'name' => 'Updated Name',
        ])->assertRedirect(route('users.index', ['tab' => 'permissions']));

        $this->assertDatabaseHas('permissions', ['id' => $permission->id, 'name' => 'Updated Name']);
    }

    public function test_can_delete_a_permission()
    {
        $permission = Permission::create(['name' => 'Test Permission']);
        $this->delete(route('permissions.destroy', $permission))
            ->assertRedirect(route('users.index', ['tab' => 'permissions']));

        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }
}
