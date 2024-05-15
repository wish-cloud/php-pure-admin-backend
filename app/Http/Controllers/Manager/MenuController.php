<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function list(Request $request)
    {
        //获取后循环处理数据
        $list = Permission::query()->where('guard_name', $request->input('guard_name', 'dashboard'))->when($request->has('keyword'), function ($query) use ($request) {
            $keyword = $request->input('keyword');
            if (! empty($keyword)) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%')
                        ->orWhere('title', 'like', '%'.$keyword.'%')
                        ->orWhere('path', 'like', '%'.$keyword.'%');
                });
            }
        })->get();

        return $this->success(new MenuResource($list));
    }

    public function create(Request $request)
    {
        $data = [];
        $data['guard_name'] = $request->input('guard_name', 'dashboard');
        $data['type'] = $request->input('menuType', 'menu');
        $data['parent_id'] = $request->input('parentId') ?? 0;
        $data['title'] = $request->input('title') ?? '';
        $data['name'] = $request->input('name') ?: '';
        $data['path'] = $request->input('path') ?: '';
        $data['sort'] = $request->input('rank', 0);
        $data['is_show'] = $request->input('isShow') ?? false;
        $data['meta'] = [
            'redirect' => $request->input('redirect') ?: '',
            'component' => $request->input('component') ?: '',
            'icon' => $request->input('icon') ?: '',
            'extraIcon' => $request->input('extraIcon') ?: '',
            'enterTransition' => $request->input('enterTransition') ?: '',
            'activePath' => $request->input('activePath') ?: '',
            'frameSrc' => $request->input('frameSrc') ?: '',
            'frameLoading' => $request->input('frameLoading') ?? true,
            'keepAlive' => $request->input('keepAlive') ?? true,
            'hiddenTag' => $request->input('hiddenTag') ?? false,
            'fixedTag' => $request->input('fixedTag') ?? false,
            'showParent' => $request->input('showParent') ?? true,
        ];

        // 验证数据
        $validator = validator($data, [
            'guard_name' => 'required|string',
            'type' => 'required|string|in:menu,iframe,link,action',
            'parent_id' => 'required|integer',
            'title' => 'required|string',
            'name' => 'required|string',
            'sort' => 'integer',
            'is_show' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }

        // 验证上级菜单，不能是action，不能是自身或者下级
        if ($data['parent_id'] > 0) {
            $parent = Permission::query()->find($data['parent_id']);
            if (empty($parent) || $parent->guard_name !== $data['guard_name']) {
                return $this->fail('上级菜单不存在');
            }
            if ($parent->type !== 'menu') {
                return $this->fail('上级菜单类型错误');
            }
        }

        // 验证路由名称是否重复
        if (Permission::query()->where('guard_name', $data['guard_name'])->where('name', $data['name'])->exists()) {
            return $data['type'] === 'action' ? $this->fail('权限标识已存在') : $this->fail('路由名称已存在');
        }

        if (Permission::create($data)) {
            return $this->success();
        }

        return $this->fail('创建失败');
    }

    public function edit(Request $request)
    {
        $id = $request->input('id');
        $permission = Permission::query()->find($id);
        if (! $permission || $permission->guard_name !== $request->input('guard_name', 'dashboard')) {
            return $this->fail('菜单不存在');
        }

        $data = [];
        $data['guard_name'] = $request->input('guard_name', 'dashboard');
        $data['type'] = $request->input('menuType', 'menu');
        $data['parent_id'] = $request->input('parentId') ?? 0;
        $data['title'] = $request->input('title') ?? '';
        $data['name'] = $request->input('name') ?: '';
        $data['path'] = $request->input('path') ?: '';
        $data['sort'] = $request->input('rank', 0);
        $data['is_show'] = $request->input('isShow') ?? false;
        $data['meta'] = [
            'redirect' => $request->input('redirect') ?: '',
            'component' => $request->input('component') ?: '',
            'icon' => $request->input('icon') ?: '',
            'extraIcon' => $request->input('extraIcon') ?: '',
            'enterTransition' => $request->input('enterTransition') ?: '',
            'activePath' => $request->input('activePath') ?: '',
            'frameSrc' => $request->input('frameSrc') ?: '',
            'frameLoading' => $request->input('frameLoading') ?? true,
            'keepAlive' => $request->input('keepAlive') ?? true,
            'hiddenTag' => $request->input('hiddenTag') ?? false,
            'fixedTag' => $request->input('fixedTag') ?? false,
            'showParent' => $request->input('showParent') ?? true,
        ];

        // 验证数据
        $validator = validator($data, [
            'guard_name' => 'required|string',
            'type' => 'required|string|in:menu,iframe,link,action',
            'parent_id' => 'required|integer',
            'title' => 'required|string',
            'name' => 'required|string',
            'sort' => 'integer',
            'is_show' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }

        // 验证上级菜单，不能是action，不能是自身或者下级
        if ($data['parent_id'] > 0) {
            $parent = Permission::query()->find($data['parent_id']);
            if (empty($parent) || $parent->guard_name !== $data['guard_name']) {
                return $this->fail('上级菜单不存在');
            }
            if ($parent->type !== 'menu') {
                return $this->fail('上级菜单类型错误');
            }

            if ($parent->id === $id) {
                return $this->fail('上级菜单不能是自身');
            }

            if (in_array($parent->id, Permission::childrenIds($id))) {
                return $this->fail('上级菜单不能为当前下级');
            }
        }

        // 验证路由名称是否重复
        if (Permission::query()->where('guard_name', $data['guard_name'])->where('name', $data['name'])->where('id', '!=', $id)->exists()) {
            return $data['type'] === 'action' ? $this->fail('权限标识已存在') : $this->fail('路由名称已存在');
        }

        if ($permission->update($data)) {
            return $this->success();
        }

        return $this->fail('编辑失败');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $permission = Permission::query()->find($id);
        if (! $permission || $permission->guard_name !== $request->input('guard_name', 'dashboard')) {
            return $this->fail('菜单不存在');
        }
        $childrenIds = Permission::childrenIds($id);
        if (! empty($childrenIds)) {
            return $this->fail('请先删除下级菜单');
        }

        DB::beginTransaction();
        try {
            $permission->users()->detach();
            $permission->roles()->detach();
            if ($permission->delete()) {
                DB::commit();
                // @TODO 更新缓存

                return $this->success();
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $this->fail('删除失败');
    }
}
