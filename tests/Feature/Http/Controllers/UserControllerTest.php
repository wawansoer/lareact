<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_it_loads_only_users_on_initial_visit(): void
    {
        $this->get(route('users.index'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->component('users/index')
                    ->has('users')
                    ->missing('tenants')
                    ->missing('roles')
                    ->missing('permissions')
            );
    }

    public function test_it_loads_tenants_on_demand(): void
    {
        $this->get(route('users.index', ['tab' => 'tenants']))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->component('users/index')
                    ->has('tenants')
                    ->missing('users')
                    ->missing('roles')
                    ->missing('permissions')
            );
    }

    public function test_it_filters_tenants_on_demand(): void
    {
        $tenantToFind = \App\Models\Tenant::factory()->create(['name' => 'Find Me']);
        \App\Models\Tenant::factory()->create(['name' => 'Ignore Me']);

        $this->get(route('users.index', ['tab' => 'tenants', 'name' => 'Find Me']))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->component('users/index')
                    ->has('tenants.data', 1)
                    ->where('tenants.data.0.name', $tenantToFind->name)
            );
    }

    public function test_bulk_destroy_users()
    {
        $users = User::factory()->count(3)->create();
        $userIds = $users->pluck('id')->toArray();

        $response = $this->post(route('users.bulk-delete'), [
            'ids' => $userIds,
        ]);

        $response->assertRedirect(route('users.index', ['tab' => 'users']));
        $response->assertSessionHas('success', 'Selected users deleted successfully.');

        foreach ($userIds as $id) {
            $this->assertDatabaseMissing('users', ['id' => $id]);
        }
    }

    public function test_can_create_a_user()
    {
        $this->post(route('users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('users.index', ['tab' => 'users']));

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_can_update_a_user()
    {
        $user = User::factory()->create();
        $this->put(route('users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
        ])->assertRedirect(route('users.index', ['tab' => 'users']));

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    public function test_can_delete_a_user()
    {
        $user = User::factory()->create();
        $this->delete(route('users.destroy', $user))
            ->assertRedirect(route('users.index', ['tab' => 'users']));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
