<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenantPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Tenant $tenant1;

    private Tenant $tenant2;

    private Role $role1;

    private Role $role2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->user = User::factory()->create();

        // Create tenants
        $this->tenant1 = Tenant::factory()->create();
        $this->tenant2 = Tenant::factory()->create();

        // Create roles
        $this->role1 = Role::create(['name' => 'admin', 'tenant_id' => $this->tenant1->id]);
        $this->role2 = Role::create(['name' => 'editor', 'tenant_id' => $this->tenant2->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_assign_a_role_to_a_user_in_a_specific_tenant()
    {
        $this->user->assignRoleInTenant($this->role1, $this->tenant1);

        $this->assertTrue($this->user->hasRoleInTenant($this->role1, $this->tenant1));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_assign_role_to_user_in_another_tenant()
    {
        $this->user->assignRoleInTenant($this->role1, $this->tenant1);

        $this->assertFalse($this->user->hasRoleInTenant($this->role1, $this->tenant2));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_user_can_have_different_roles_in_different_tenants()
    {
        $this->user->assignRoleInTenant($this->role1, $this->tenant1);
        $this->user->assignRoleInTenant($this->role2, $this->tenant2);

        $this->assertTrue($this->user->hasRoleInTenant($this->role1, $this->tenant1));
        $this->assertTrue($this->user->hasRoleInTenant($this->role2, $this->tenant2));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_remove_a_role_from_a_user_in_a_specific_tenant()
    {
        $this->user->assignRoleInTenant($this->role1, $this->tenant1);
        $this->assertTrue($this->user->hasRoleInTenant($this->role1, $this->tenant1));

        $this->user->removeRoleInTenant($this->role1, $this->tenant1);
        $this->assertFalse($this->user->hasRoleInTenant($this->role1, $this->tenant1));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_remove_role_from_user_in_another_tenant()
    {
        $this->user->assignRoleInTenant($this->role1, $this->tenant1);
        $this->user->assignRoleInTenant($this->role2, $this->tenant2);

        $this->user->removeRoleInTenant($this->role1, $this->tenant1);

        $this->assertFalse($this->user->hasRoleInTenant($this->role1, $this->tenant1));
        $this->assertTrue($this->user->hasRoleInTenant($this->role2, $this->tenant2));
    }
}
