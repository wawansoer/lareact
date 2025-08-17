<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_bulk_destroy_roles()
    {
        $roles = collect();
        for ($i = 0; $i < 3; $i++) {
            $roles->push(Role::create(['name' => "role{$i}", 'guard_name' => 'web']));
        }
        $roleIds = $roles->pluck('id')->toArray();

        $response = $this->post(route('roles.bulk-delete'), [
            'ids' => $roleIds,
        ]);

        $response->assertRedirect(route('users.index', ['tab' => 'roles']));
        $response->assertSessionHas('success', 'Selected roles deleted successfully.');

        foreach ($roleIds as $id) {
            $this->assertDatabaseMissing('roles', ['id' => $id]);
        }
    }

    public function test_can_create_a_role()
    {
        $this->post(route('roles.store'), [
            'name' => 'Test Role',
        ])->assertRedirect(route('users.index', ['tab' => 'roles']));

        $this->assertDatabaseHas('roles', ['name' => 'Test Role']);
    }

    public function test_can_update_a_role()
    {
        $role = Role::create(['name' => 'Test Role']);
        $this->put(route('roles.update', $role), [
            'name' => 'Updated Name',
        ])->assertRedirect(route('users.index', ['tab' => 'roles']));

        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'Updated Name']);
    }

    public function test_can_delete_a_role()
    {
        $role = Role::create(['name' => 'Test Role']);
        $this->delete(route('roles.destroy', $role))
            ->assertRedirect(route('users.index', ['tab' => 'roles']));

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}
