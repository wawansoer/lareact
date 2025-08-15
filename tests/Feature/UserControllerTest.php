<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_loads_only_users_on_initial_visit(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.index'))
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
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.index', ['tab' => 'tenants']))
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
        $user = User::factory()->create();
        $tenantToFind = \App\Models\Tenant::factory()->create(['name' => 'Find Me']);
        \App\Models\Tenant::factory()->create(['name' => 'Ignore Me']);

        $this->actingAs($user)
            ->get(route('users.index', ['tab' => 'tenants', 'name' => 'Find Me']))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->component('users/index')
                    ->has('tenants.data', 1)
                    ->where('tenants.data.0.name', $tenantToFind->name)
            );
    }
}
