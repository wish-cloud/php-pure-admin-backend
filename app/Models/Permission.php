<?php

namespace App\Models;

use App\Exceptions\PermissionDoesNotExist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'guard_name',
        'parent_id',
        'type',
        'title',
        'name',
        'path',
        'sort',
        'meta',
        'is_show',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_show' => 'boolean',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_has_permissions',
            'permission_id',
            'role_id'
        );
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_has_permissions',
            'permission_id',
            'user_id'
        );
    }

    public static function findByName(string $name, ?string $guardName = null)
    {
        $guardName = $guardName ?? 'dashboard';
        $permission = self::query()->where('code', $name)->where('guard_name', $guardName)->first();
        if (! $permission) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }

        return $permission;
    }

    public static function childrenIds($id)
    {
        $ids = [];
        $children = self::query()->where('parent_id', $id)->get(['id']);
        foreach ($children as $child) {
            $ids = array_merge($ids, [$child->id], self::childrenIds($child->id));
        }

        return $ids;
    }
}
