<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $this->actingAs($this->user);
});

test('TenantController index lists tenants for authorized user', function () {
    Tenant::factory()->count(2)->create();
    $response = $this->get('/tenants');
    $response->assertStatus(200);
});

test('TenantController store validates required fields', function () {
    $response = $this->post('/tenants', [
        'name' => '',
        'description' => 'desc',
    ]);
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['name']);
});

test('TenantController stores a new tenant', function () {
    $payload = ['name' => 'Tenant One', 'description' => 'desc'];
    $response = $this->post('/tenants', $payload);
    $response->assertRedirect('/tenants');
    $this->assertDatabaseHas('tenants', ['name' => 'Tenant One']);
});

test('TenantController updates an existing tenant', function () {
    $tenant = Tenant::factory()->create(['name' => 'Old Name']);
    $response = $this->put("/tenants/{$tenant->id}", [
        'name' => 'New Name',
        'description' => 'desc',
    ]);
    $response->assertRedirect('/tenants');
    $tenant->refresh();
    expect($tenant->name)->toBe('New Name');
});

test('TenantController deletes a tenant', function () {
    $tenant = Tenant::factory()->create();
    $response = $this->delete("/tenants/{$tenant->id}");
    $response->assertRedirect('/tenants');
    $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
});

test('TenantController bulk deletes tenants', function () {
    $tenants = Tenant::factory()->count(3)->create();
    $ids = $tenants->pluck('id')->toArray();
    $response = $this->post('/tenants/bulk-delete', ['ids' => $ids]);
    $response->assertRedirect('/tenants');
    foreach ($tenants as $tenant) {
        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }
});

test('TenantController show route returns 200 for authorized user', function () {
    $tenant = Tenant::factory()->create(['name' => 'Show Me']);
    $response = $this->get("/tenants/{$tenant->id}");
    $response->assertStatus(200);
});

test('TenantController create route returns 200 for authorized user', function () {
    $response = $this->get('/tenants/create');
    $response->assertStatus(200);
});

test('TenantController edit route returns 200 for authorized user', function () {
    $tenant = Tenant::factory()->create();
    $response = $this->get("/tenants/{$tenant->id}/edit");
    $response->assertStatus(200);
});

test('TenantController bulk destroy validates ids presence', function () {
    $response = $this->post('/tenants/bulk-delete', []);
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['ids']);
});

test('TenantController update validation requires name', function () {
    $tenant = Tenant::factory()->create(['name' => 'Old Name']);
    $response = $this->put("/tenants/{$tenant->id}", [
        'name' => '',
        'description' => 'desc',
    ]);
    $response->assertSessionHasErrors(['name']);
});

test('TenantController update validation name max length', function () {
    $tenant = Tenant::factory()->create(['name' => 'Old Name']);
    $response = $this->put("/tenants/{$tenant->id}", [
        'name' => str_repeat('a', 256),
        'description' => 'desc',
    ]);
    $response->assertSessionHasErrors(['name']);
});

test('TenantController store validates unique name', function () {
    $existing = Tenant::factory()->create(['name' => 'UniqueTenant']);
    $response = $this->post('/tenants', ['name' => 'UniqueTenant', 'description' => 'desc']);
    $response->assertSessionHasErrors(['name']);
});

test('guest cannot access tenants index', function () {
    Auth::logout();
    $response = $this->get('/tenants');
    $response->assertRedirect('/login');
});

test('guest cannot access show/create/edit routes', function () {
    Auth::logout();
    $tenant = Tenant::factory()->create();
    $response1 = $this->get("/tenants/{$tenant->id}");
    $response1->assertRedirect('/login');
    $response2 = $this->get('/tenants/create');
    $response2->assertRedirect('/login');
    $response3 = $this->get("/tenants/{$tenant->id}/edit");
    $response3->assertRedirect('/login');
});

test('TenantController show returns 404 for non-existent tenant', function () {
    $response = $this->get('/tenants/99999999999999999999999999');
    $response->assertStatus(404);
});

test('guest cannot bulk delete tenants', function () {
    Auth::logout();
    $response = $this->post('/tenants/bulk-delete', ['ids' => []]);
    $response->assertRedirect('/login');
});

test('TenantController update rejects duplicate name across tenants', function () {
    $t1 = Tenant::factory()->create(['name' => 'Tenant A']);
    $t2 = Tenant::factory()->create(['name' => 'Tenant B']);
    $response = $this->put("/tenants/{$t2->id}", [
        'name' => 'Tenant A',
        'description' => 'desc',
    ]);
    $response->assertSessionHasErrors(['name']);
});

test('TenantController store allows missing description', function () {
    $response = $this->post('/tenants', ['name' => 'Tenant OnlyName']);
    $response->assertRedirect('/tenants');
    $this->assertDatabaseHas('tenants', ['name' => 'Tenant OnlyName']);
});

test('TenantController update allows setting same name (no validation error)', function () {
    $tenant = Tenant::factory()->create(['name' => 'SameName']);
    $response = $this->put("/tenants/{$tenant->id}", [
        'name' => 'SameName',
        'description' => 'updated',
    ]);
    $response->assertRedirect('/tenants');
    $tenant->refresh();
    expect($tenant->name)->toBe('SameName');
    expect($tenant->description)->toBe('updated');
});

test('TenantController bulk-delete with invalid IDs returns validation error', function () {
    Tenant::factory()->count(2)->create();
    $response = $this->post('/tenants/bulk-delete', ['ids' => ['not-a-id']]);
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['ids.0']);
});
