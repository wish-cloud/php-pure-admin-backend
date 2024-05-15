<?php

namespace App\Traits;

use App\Exceptions\PermissionDoesNotExist;
use App\Models\Permission;
use App\Models\Role;

trait HasPermissions
{
    public function isSuperManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function hasPermission($permission): bool
    {
        return true;
        $permission = Permission::findByName(
            $permission, $this->getGuardName()
        );

        if (! $permission instanceof Permission) {
            throw new PermissionDoesNotExist();
        }

        return true;
    }

    public function hasRole($roles, $needAll = false): bool
    {
        $this->loadMissing('roles');

        if (is_string($roles) && strpos($roles, '|') !== false) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $this->roles->contains('code', $roles);
        }

        if (is_int($roles)) {
            return $this->roles->contains('id', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains($roles->getKeyName(), $roles->getKey());
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($needAll) {
                    if (! $this->hasRole($role)) {
                        return false;
                    }
                } else {
                    if ($this->hasRole($role)) {
                        return true;
                    }
                }
            }

            return false;
        }

        return $roles->intersect($this->roles)->isNotEmpty();
    }

    public function syncRoles(...$roles)
    {
        $this->roles()->detach();

        return $this->assignRole($roles);
    }

    public function assignRole(...$roles)
    {

        $roles = collect($roles)
            ->flatten()
            ->reduce(function ($array, $role) {
                if (empty($role)) {
                    return $array;
                }

                $role = $this->getStoredRole($role);
                if (! $role instanceof Role) {
                    return $array;
                }

                if ($this->getGuardName() !== $role->guard_name) {
                    throw new PermissionDoesNotExist();
                }

                $array[$role->getKey()] = ['guard_name' => $role->guard_name];

                return $array;
            }, []);

        $this->roles()->sync($roles, false);

        // @TODO 清理缓存

        return $this;
    }

    protected function getStoredRole($role)
    {
        if (is_numeric($role)) {
            return Role::query()->where('id', $role)->where('guard_name', $this->getGuardName())->first();
        }

        if (is_string($role)) {
            return Role::query()->where('code', $role)->where('guard_name', $this->getGuardName())->first();
        }

        return $role;
    }

    protected function convertPipeToArray(string $pipeString)
    {
        $pipeString = trim($pipeString);

        if (strlen($pipeString) <= 2) {
            return $pipeString;
        }

        $quoteCharacter = substr($pipeString, 0, 1);
        $endCharacter = substr($quoteCharacter, -1, 1);

        if ($quoteCharacter !== $endCharacter) {
            return explode('|', $pipeString);
        }

        if (! in_array($quoteCharacter, ["'", '"'])) {
            return explode('|', $pipeString);
        }

        return explode('|', trim($pipeString, $quoteCharacter));
    }

    protected function getGuardName(): string
    {
        return $this->guard_name ?? 'dashboard';
    }
}
