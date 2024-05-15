<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function list(Request $request)
    {
        $list = Role::query()->where('guard_name', $request->input('guard_name', 'dashboard'))->when($request->has('keyword'), function ($query) use ($request) {
            $keyword = $request->input('keyword');
            if (! empty($keyword)) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%')
                        ->orWhere('code', 'like', '%'.$keyword.'%');
                });
            }
        })->paginate($request->input('limit', 10));

        return $this->success(new JsonResource($list));
    }

    public function all(Request $request)
    {
        $roles = Role::query()->where('guard_name', $request->input('guard_name', 'dashboard'))->get(['id', 'name']);

        return $this->success(new JsonResource($roles));
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100|unique:roles,code,NULL,id,guard_name,'.$request->input('guard_name', 'dashboard'),
            'remark' => 'string|max:100',
        ]);

        if (Role::query()->create($data)) {
            return $this->success();
        }

        return $this->fail('创建失败');
    }

    public function edit(Request $request)
    {
        $id = $request->input('id');
        $role = Role::query()->find($id);
        if (! $role) {
            return $this->fail('用户不存在');
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100|unique:roles,code,'.$id.',id,guard_name,'.$request->input('guard_name', 'dashboard'),
            'remark' => 'string|max:100',
        ]);

        if ($role->update($data)) {
            return $this->success();
        }

        return $this->fail('编辑失败');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $role = Role::query()->find($id);
        if (! $role) {
            return $this->fail('用户不存在');
        }

        if ($role->code == 'manager') {
            return $this->fail('超级管理员角色不允许删除');
        }

        DB::beginTransaction();
        try {
            $role->users()->detach();
            $role->permissions()->detach();
            if ($role->delete()) {
                DB::commit();
                // @TODO 更新缓存

                return $this->success();
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $this->fail('删除失败');
    }

    public function menuIds(Request $request)
    {
        $role = Role::query()->find($request->input('id'));
        if (! $role || $role->guard_name !== $request->input('guard_name', 'dashboard')) {
            return $this->fail('角色不存在');
        }

        return $this->success($role->permissions()->pluck('id'));
    }

    public function setMenu(Request $request)
    {
        $role = Role::query()->find($request->input('id'));
        if (! $role || $role->guard_name !== $request->input('guard_name', 'dashboard')) {
            return $this->fail('角色不存在');
        }

        $menuIds = $request->input('menuIds', []);
        $role->permissions()->sync($menuIds);

        return $this->success();
    }
}
