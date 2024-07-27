<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

trait HasPermissionsTrait
{
  public function givePermissionTo(...$permissions)
  {
    $permissions = $this->getAllPermissions(Arr::flatten($permissions));

    if ($permissions === null) {
      return $this;
    }

    $this->permissions()->saveMany($permissions);

    return $this;
  }

  public function withdrawPermissionTo(...$permissions)
  {
    $permissions = $this->getAllPermissions(Arr::flatten($permissions));
    $this->permissions()->detach($permissions);
    return $this;
  }

  public function updatePermissions(...$permissions)
  {
    $this->permissions()->detach();
    return $this->givePermissionTo($permissions);
  }

  public function hasRole(...$roles)
  {
    foreach ($roles as $role) {
      if ($this->roles->contains('name', $role)) {
        return true;
      }
    }

    return false;
  }

  public function hasPermissionTo($permission)
  {
    return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
  }

  protected function hasPermissionThroughRole($permission)
  {
    foreach ($permission->roles as $role) {
      if ($this->roles->contains($role)) {
        return true;
      }
    }

    return false;
  }

  protected function hasPermission($permission)
  {
    return (bool) $this->permissions->where('name', $permission->name)->count();
  }


  protected function getAllPermissions(array $permissions)
  {
    return Permission::whereIn('name', $permissions)->get();
  }
}
