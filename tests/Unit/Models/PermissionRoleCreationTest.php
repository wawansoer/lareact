<?php

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionRoleCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_and_role_creation()
    {
        $permission = Permission::create(['name' => 'test permission', 'guard_name' => 'web']);
        $this->assertNotNull($permission->id);

        $role = Role::create(['name' => 'test role', 'guard_name' => 'web']);
        $this->assertNotNull($role->id);
    }
}
