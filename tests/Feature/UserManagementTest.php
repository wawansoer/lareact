<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can view user management page', function () {
    $this->get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/index')
            ->has('users')
            ->missing('tenants')
            ->missing('roles')
            ->missing('permissions')
        );
});

// User tests
test('can create a user', function () {
    $this->post(route('users.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('users.index', ['tab' => 'users']));

    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

test('can update a user', function () {
    $user = User::factory()->create();
    $this->put(route('users.update', $user), [
        'name' => 'Updated Name',
        'email' => $user->email,
    ])->assertRedirect(route('users.index', ['tab' => 'users']));

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
});

test('can delete a user', function () {
    $user = User::factory()->create();
    $this->delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index', ['tab' => 'users']));

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

// Tenant tests
test('can create a tenant', function () {
    $this->post(route('tenants.store'), [
        'name' => 'Test Tenant',
    ])->assertRedirect(route('users.index', ['tab' => 'tenants']));

    $this->assertDatabaseHas('tenants', ['name' => 'Test Tenant']);
});

test('can update a tenant', function () {
    $tenant = Tenant::factory()->create();
    $this->put(route('tenants.update', $tenant), [
        'name' => 'Updated Name',
    ])->assertRedirect(route('users.index', ['tab' => 'tenants']));

    $this->assertDatabaseHas('tenants', ['id' => $tenant->id, 'name' => 'Updated Name']);
});

test('can delete a tenant', function () {
    $tenant = Tenant::factory()->create();
    $this->delete(route('tenants.destroy', $tenant))
        ->assertRedirect(route('users.index', ['tab' => 'tenants']));

    $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
});

// Role tests
test('can create a role', function () {
    $this->post(route('roles.store'), [
        'name' => 'Test Role',
    ])->assertRedirect(route('users.index', ['tab' => 'roles']));

    $this->assertDatabaseHas('roles', ['name' => 'Test Role']);
});

test('can update a role', function () {
    $role = Role::create(['name' => 'Test Role']);
    $this->put(route('roles.update', $role), [
        'name' => 'Updated Name',
    ])->assertRedirect(route('users.index', ['tab' => 'roles']));

    $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'Updated Name']);
});

test('can delete a role', function () {
    $role = Role::create(['name' => 'Test Role']);
    $this->delete(route('roles.destroy', $role))
        ->assertRedirect(route('users.index', ['tab' => 'roles']));

    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});

// Permission tests
test('can create a permission', function () {
    $this->post(route('permissions.store'), [
        'name' => 'Test Permission',
    ])->assertRedirect(route('users.index', ['tab' => 'permissions']));

    $this->assertDatabaseHas('permissions', ['name' => 'Test Permission']);
});

test('can update a permission', function () {
    $permission = Permission::create(['name' => 'Test Permission']);
    $this->put(route('permissions.update', $permission), [
        'name' => 'Updated Name',
    ])->assertRedirect(route('users.index', ['tab' => 'permissions']));

    $this->assertDatabaseHas('permissions', ['id' => $permission->id, 'name' => 'Updated Name']);
});

test('can delete a permission', function () {
    $permission = Permission::create(['name' => 'Test Permission']);
    $this->delete(route('permissions.destroy', $permission))
        ->assertRedirect(route('users.index', ['tab' => 'permissions']));

    $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
});
