<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function list(Request $request)
    {
        $users = User::query()->with('roles:id,name')->when($request->has('status'), function ($query) use ($request) {
            $status = $request->input('status');
            if (! is_null($status)) {
                $query->where('status', $status);
            }
        })->when($request->has('name'), function ($query) use ($request) {
            $name = $request->input('name');
            if (! empty($name)) {
                $query->where('name', 'like', '%'.$name.'%');
            }
        })->when($request->has('email'), function ($query) use ($request) {
            $email = $request->input('email');
            if (! empty($email)) {
                $query->where('email', 'like', '%'.$email.'%');
            }
        })->paginate($request->input('limit', 10));

        return $this->success(new JsonResource($users));
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'status' => 'required|in:0,1',
        ]);

        $data['password'] = Hash::make($data['password']);

        if (User::factory()->create($data)) {
            return $this->success();
        }

        return $this->fail('创建失败');
    }

    public function edit(Request $request)
    {
        $id = $request->input('id');
        $user = User::query()->find($id);
        if (! $user) {
            return $this->fail('用户不存在');
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$id.',id',
            'status' => 'required|in:0,1',
        ]);

        if ($user->update($data)) {
            return $this->success();
        }

        return $this->fail('编辑失败');
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
            'password' => 'required|string|min:6',
        ]);

        $user = User::query()->find($data['id']);
        if (! $user) {
            return $this->fail('用户不存在');
        }

        $user->password = Hash::make($data['password']);

        if ($user->save()) {
            return $this->success();
        }

        return $this->fail('重置密码失败');
    }

    public function changeStatus(Request $request)
    {

        $data = $request->validate([
            'id' => 'required|integer',
        ]);

        $user = User::query()->find($data['id']);
        if (! $user) {
            return $this->fail('用户不存在');
        }

        if ($user->id === 1 && $user->status === 1) {
            return $this->fail('不允许禁用超级管理员');
        }

        if ($user->id === auth()->user()->id) {
            return $this->fail('不允许禁用自己');
        }

        $user->status = $user->status === 1 ? 0 : 1;

        if ($user->save()) {
            return $this->success();
        }

        return $this->fail('修改状态失败');
    }

    public function delete(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
        ]);

        $user = User::query()->find($data['id']);
        if (! $user) {
            return $this->fail('用户不存在');
        }

        if ($user->id === 1) {
            return $this->fail('不允许删除超级管理员');
        }

        if ($user->id === auth()->user()->id) {
            return $this->fail('不允许删除自己');
        }

        DB::beginTransaction();
        try {
            $user->roles()->detach();
            $user->permissions()->detach();
            if ($user->delete()) {
                DB::commit();

                return $this->success();
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $this->fail('删除失败');
    }

    public function batchDelete(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
        ]);

        $ids = $data['ids'];
        if (in_array(1, $ids)) {
            return $this->fail('不允许删除超级管理员');
        }

        if (in_array(auth()->user()->id, $ids)) {
            return $this->fail('不允许删除自己');
        }

        DB::beginTransaction();
        try {
            User::query()->whereIn('id', $ids)->get()->each(function ($user) {
                $user->roles()->detach();
                $user->permissions()->detach();
                $user->delete();
            });

            DB::commit();

            return $this->success();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return $this->fail('删除失败');
    }

    public function assignRole(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
            'roles' => 'required|array',
        ]);

        $user = User::query()->find($data['id']);
        if (! $user) {
            return $this->fail('用户不存在');
        }

        if ($user->id === 1) {
            return $this->fail('不允许设置超级管理员');
        }

        if ($user->id === auth()->user()->id) {
            return $this->fail('不允许设置自己');
        }

        $user->syncRoles($data['roles']);

        return $this->success();
    }
}
