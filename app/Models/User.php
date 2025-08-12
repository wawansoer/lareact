<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, HasUlids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class);
    }

    /**
     * Override the roles relationship to add tenant context.
     */
    public function roles(): MorphToMany
    {
        return $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            config('permission.column_names.role_pivot_key')
        )->withPivot('tenant_id');
    }

    /**
     * Assign a role to the user within a specific tenant.
     *
     * @return $this
     */
    public function assignRoleInTenant(Role|string $role, Tenant|string $tenant): self
    {
        $tenantId = $this->getTenantId($tenant);
        $roleInstance = $this->getRoleInstance($role, $tenantId);

        $this->roles()->attach($roleInstance->id, ['tenant_id' => $tenantId]);
        $this->forgetCachedPermissions();

        return $this;
    }

    /**
     * Check if the user has the given role in the given tenant.
     */
    public function hasRoleInTenant(Role|string $role, Tenant|string $tenant): bool
    {
        $tenantId = $this->getTenantId($tenant);
        $roleName = $role instanceof Role ? $role->name : $role;

        return $this->roles()
            ->where('name', $roleName)
            ->wherePivot('tenant_id', $tenantId)
            ->exists();
    }

    /**
     * Remove a role from the user within a specific tenant.
     *
     * @return $this
     */
    public function removeRoleInTenant(Role|string $role, Tenant|string $tenant): self
    {
        $tenantId = $this->getTenantId($tenant);
        $roleInstance = $this->getRoleInstance($role, $tenantId);

        if ($roleInstance) {
            $this->roles()->wherePivot('tenant_id', $tenantId)->detach($roleInstance);
            $this->forgetCachedPermissions();
        }

        return $this;
    }

    /**
     * Get the tenant ID from a Tenant model or string.
     */
    private function getTenantId(Tenant|string $tenant): string
    {
        return $tenant instanceof Tenant ? $tenant->id : $tenant;
    }

    /**
     * Get a role instance.
     */
    private function getRoleInstance(Role|string $role, string $tenantId): Role
    {
        if ($role instanceof Role) {
            return $role;
        }

        return Role::where('name', $role)
            ->where('guard_name', $this->getDefaultGuardName())
            ->where(function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId)
                    ->orWhereNull('tenant_id');
            })
            ->firstOrFail();
    }
}
