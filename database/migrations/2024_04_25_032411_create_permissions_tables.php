<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('guard_name', 125)->default('dashboard')->comment('守卫名称');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级菜单');
            $table->string('type', 50)->comment('类型 menu, iframe, link，action');
            $table->string('title', 125)->comment('菜单名称');
            $table->string('name', 125)->comment('路由名称');
            $table->string('path', 125)->default('')->comment('菜单路径/链接');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->json('meta')->nullable()->comment('元数据');
            $table->boolean('is_show')->default(true)->comment('是否显示');
            $table->timestamps();

            $table->index(['name', 'guard_name']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id'); // role id
            $table->string('name', 125)->comment('角色名称');
            $table->string('code', 125)->comment('角色标识');
            $table->string('guard_name', 125)->default('dashboard')->comment('守卫名称');
            $table->string('remark', 500)->nullable()->comment('备注');
            $table->timestamps();
            $table->unique(['code', 'guard_name']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')
                ->references('id') // permission id
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id') // role id
                ->on('roles')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
        });

        Schema::create('user_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('guard_name');
            $table->unsignedBigInteger('user_id');
            $table->index(['user_id', 'guard_name'], 'model_has_permissions_model_id_guard_name_index');

            $table->foreign('permission_id')
                ->references('id') // permission id
                ->on('permissions')
                ->onDelete('cascade');
            $table->primary(['permission_id', 'user_id', 'guard_name'],
                'model_has_permissions_permission_guard_name_primary');

        });

        Schema::create('user_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');

            $table->string('guard_name');
            $table->unsignedBigInteger('user_id');
            $table->index(['user_id', 'guard_name'], 'model_has_roles_model_id_guard_name_index');

            $table->foreign('role_id')
                ->references('id') // role id
                ->on('roles')
                ->onDelete('cascade');
            $table->primary(['role_id', 'user_id', 'guard_name'],
                'model_has_roles_role_guard_name_primary');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('user_has_permissions');
        Schema::dropIfExists('user_has_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
