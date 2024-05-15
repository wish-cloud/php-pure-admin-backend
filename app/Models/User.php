<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasPermissions, Notifiable;

    protected $guard_name = 'dashboard';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    public function guardName()
    {
        return $this->guard_name;
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_has_permissions', 'user_id', 'permission_id')->wherePivot('guard_name', $this->guard_name);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_has_roles', 'user_id', 'role_id')->wherePivot('guard_name', $this->guard_name);
    }

    public static function getMenusWithHierarchy($user)
    {
        $guardName = $user->guard_name ?? 'dashboard';
        // 获取所属角色拥有的权限
        $permissionIds = [];
        if ($user->isSuperManager()) {
            // 超级管理员获取全部权限ID
            $permissionIds = Permission::query()->where('guard_name', $guardName)->pluck('id')->toArray();
        } else {
            foreach ($user->roles as $role) {
                $permissionIds = array_merge($permissionIds, $role->permissions()->pluck('id')->toArray());
            }
            // 附加用户直接拥有的权限ID
            $additionalPermissionIds = $user->permissions()->pluck('id')->toArray();
            $permissionIds = array_merge($permissionIds, $additionalPermissionIds);
        }
        $permissions = Permission::query()->whereIn('id', $permissionIds)->get()->toArray();

        // 获取所有权限，用于查找父级菜单
        $allPermissions = Permission::query()->where('guard_name', $guardName)->get()->toArray();

        // 构造树型结构数据
        return self::handleTree($allPermissions, $permissions);
    }

    public static function handleTree($allPermissions, $userPermissions)
    {
        $childrenListMap = [];
        $nodeIds = [];
        $tree = [];

        // 构建 childrenListMap 和 nodeIds
        foreach ($allPermissions as $d) {
            $parentId = $d['parent_id'];
            if (! isset($childrenListMap[$parentId])) {
                $childrenListMap[$parentId] = [];
            }
            $nodeIds[$d['id']] = $d;
            $childrenListMap[$parentId][] = self::generateMenuData($d);
        }

        // 构建树的顶级节点
        foreach ($userPermissions as $d) {
            $parentId = $d['parent_id'];
            if (! isset($nodeIds[$parentId])) {
                $tree[] = self::generateMenuData($d);
            }
        }

        // 构建树的子节点
        foreach ($tree as &$t) {
            self::adaptToChildrenList($t, $childrenListMap);
        }

        return $tree;
    }

    private static function adaptToChildrenList(&$o, $childrenListMap)
    {
        if (isset($childrenListMap[$o['id']])) {
            $o['children'] = $childrenListMap[$o['id']];
            foreach ($o['children'] as &$child) {
                self::adaptToChildrenList($child, $childrenListMap);
            }
        }
    }

    public static function generateMenuData($item)
    {
        $data = [
            'id' => $item['id'],
            'parent_id' => $item['parent_id'],
            'path' => $item['path'],
            'name' => $item['name'],
        ];
        if (! empty($item['meta']['component'])) {
            $data['component'] = $item['meta']['component'];
        }
        $data['meta'] = $item['meta'];
        $data['meta']['rank'] = $item['sort'];
        $data['meta']['title'] = $item['title'];
        $data['meta']['isShow'] = $item['is_show'];

        unset($data['meta']['component']);

        return $data;
    }
}
