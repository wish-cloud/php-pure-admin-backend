<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'James',
            'email' => '344911577@qq.com',
            'password' => bcrypt('123456'),
            'status' => 1,
        ]);

        Role::query()->create([
            'name' => '超级管理员',
            'code' => 'manager',
            'guard_name' => 'dashboard',
        ]);

        Role::query()->create([
            'name' => '管理员',
            'code' => 'admin',
            'guard_name' => 'dashboard',
        ]);

        $admin->assignRole(['manager']);

        $prefix = Schema::getConnection()->getTablePrefix();
        $tableName = $prefix.'permissions';

        DB::statement("INSERT INTO {$tableName} (`id`, `guard_name`, `parent_id`, `type`, `title`, `name`, `path`, `sort`, `meta`, `is_show`, `created_at`, `updated_at`) VALUES (1, 'dashboard', 0, 'menu', '系统管理', '/manager', '/manager', 99, '{\"icon\": \"ri:settings-3-line\", \"fixedTag\": false, \"frameSrc\": \"\", \"redirect\": \"\", \"component\": \"\", \"extraIcon\": \"\", \"hiddenTag\": false, \"keepAlive\": false, \"activePath\": \"\", \"showParent\": true, \"frameLoading\": true, \"enterTransition\": \"\"}', 1, '2024-05-15 03:03:04', '2024-05-15 08:58:42');");
        DB::statement("INSERT INTO {$tableName} (`id`, `guard_name`, `parent_id`, `type`, `title`, `name`, `path`, `sort`, `meta`, `is_show`, `created_at`, `updated_at`) VALUES (2, 'dashboard', 1, 'menu', '用户管理', 'ManagerUser', '/manager/user', 99, '{\"icon\": \"ri:admin-line\", \"fixedTag\": false, \"frameSrc\": \"\", \"redirect\": \"\", \"component\": \"/manager/user/index\", \"extraIcon\": \"\", \"hiddenTag\": false, \"keepAlive\": false, \"activePath\": \"\", \"showParent\": true, \"frameLoading\": true, \"enterTransition\": \"\"}', 1, '2024-05-15 03:04:15', '2024-05-15 08:59:15');");
        DB::statement("INSERT INTO {$tableName} (`id`, `guard_name`, `parent_id`, `type`, `title`, `name`, `path`, `sort`, `meta`, `is_show`, `created_at`, `updated_at`) VALUES (3, 'dashboard', 1, 'menu', '角色管理', 'ManagerRole', '/manager/role', 99, '{\"icon\": \"ri:admin-fill\", \"fixedTag\": false, \"frameSrc\": \"\", \"redirect\": \"\", \"component\": \"/manager/role/index\", \"extraIcon\": \"\", \"hiddenTag\": false, \"keepAlive\": false, \"activePath\": \"\", \"showParent\": true, \"frameLoading\": true, \"enterTransition\": \"\"}', 1, '2024-05-15 03:04:46', '2024-05-15 08:59:18');");
        DB::statement("INSERT INTO {$tableName} (`id`, `guard_name`, `parent_id`, `type`, `title`, `name`, `path`, `sort`, `meta`, `is_show`, `created_at`, `updated_at`) VALUES (4, 'dashboard', 1, 'menu', '菜单管理', 'ManagerMenu', '/manager/menu', 99, '{\"icon\": \"ri:menu-add-fill\", \"fixedTag\": false, \"frameSrc\": \"\", \"redirect\": \"\", \"component\": \"/manager/menu/index\", \"extraIcon\": \"\", \"hiddenTag\": false, \"keepAlive\": false, \"activePath\": \"\", \"showParent\": true, \"frameLoading\": true, \"enterTransition\": \"\"}', 1, '2024-05-15 03:05:43', '2024-05-15 08:59:21');");
        DB::statement("INSERT INTO {$tableName} (`id`, `guard_name`, `parent_id`, `type`, `title`, `name`, `path`, `sort`, `meta`, `is_show`, `created_at`, `updated_at`) VALUES (5, 'dashboard', 0, 'menu', '系统信息', 'About', '/about', 99, '{\"icon\": \"ri:information-line\", \"fixedTag\": false, \"frameSrc\": \"\", \"redirect\": \"\", \"component\": \"/about/index\", \"extraIcon\": \"\", \"hiddenTag\": false, \"keepAlive\": false, \"activePath\": \"\", \"showParent\": true, \"frameLoading\": true, \"enterTransition\": \"\"}', 1, '2024-05-15 03:07:45', '2024-05-15 08:58:46');");
        DB::statement("INSERT INTO {$tableName} (`id`, `guard_name`, `parent_id`, `type`, `title`, `name`, `path`, `sort`, `meta`, `is_show`, `created_at`, `updated_at`) VALUES (6, 'dashboard', 0, 'menu', '外部页面', '/iframe', '/iframe', 100, '{\"icon\": \"ri:links-fill\", \"fixedTag\": false, \"frameSrc\": \"\", \"redirect\": \"\", \"component\": \"\", \"extraIcon\": \"\", \"hiddenTag\": false, \"keepAlive\": false, \"activePath\": \"\", \"showParent\": true, \"frameLoading\": true, \"enterTransition\": \"\"}', 1, '2024-05-15 08:05:11', '2024-05-15 08:58:50');");
        DB::statement("INSERT INTO {$tableName} (`id`, `guard_name`, `parent_id`, `type`, `title`, `name`, `path`, `sort`, `meta`, `is_show`, `created_at`, `updated_at`) VALUES (7, 'dashboard', 6, 'menu', '内嵌文档', '/iframe/embedded', '/iframe/embedded', 99, '{\"icon\": \"\", \"fixedTag\": false, \"frameSrc\": \"\", \"redirect\": \"\", \"component\": \"\", \"extraIcon\": \"\", \"hiddenTag\": false, \"keepAlive\": false, \"activePath\": \"\", \"showParent\": true, \"frameLoading\": true, \"enterTransition\": \"\"}', 1, '2024-05-15 08:06:06', '2024-05-15 08:59:27');");
        DB::statement("INSERT INTO {$tableName} (`id`, `guard_name`, `parent_id`, `type`, `title`, `name`, `path`, `sort`, `meta`, `is_show`, `created_at`, `updated_at`) VALUES (8, 'dashboard', 6, 'menu', '外部链接', '/iframe/external', '/iframe/external', 99, '{\"icon\": \"\", \"fixedTag\": false, \"frameSrc\": \"\", \"redirect\": \"\", \"component\": \"\", \"extraIcon\": \"\", \"hiddenTag\": false, \"keepAlive\": false, \"activePath\": \"\", \"showParent\": true, \"frameLoading\": true, \"enterTransition\": \"\"}', 1, '2024-05-15 08:06:29', '2024-05-15 08:59:31');");
        DB::statement("INSERT INTO {$tableName} (`id`, `guard_name`, `parent_id`, `type`, `title`, `name`, `path`, `sort`, `meta`, `is_show`, `created_at`, `updated_at`) VALUES (9, 'dashboard', 7, 'iframe', 'Element Plus', 'FrameElementPlus', '/iframe/ep', 99, '{\"icon\": \"\", \"fixedTag\": false, \"frameSrc\": \"https://element-plus.org/zh-CN/\", \"redirect\": \"\", \"component\": \"\", \"extraIcon\": \"\", \"hiddenTag\": false, \"keepAlive\": true, \"activePath\": \"\", \"showParent\": true, \"frameLoading\": true, \"enterTransition\": \"\"}', 1, '2024-05-15 08:07:49', '2024-05-15 08:59:39');");
        DB::statement("INSERT INTO {$tableName} (`id`, `guard_name`, `parent_id`, `type`, `title`, `name`, `path`, `sort`, `meta`, `is_show`, `created_at`, `updated_at`) VALUES (10, 'dashboard', 7, 'iframe', 'Tailwindcss', 'FrameTailwindcss', '/iframe/tailwindcss', 99, '{\"icon\": \"\", \"fixedTag\": false, \"frameSrc\": \"https://tailwindcss.com/docs/installation\", \"redirect\": \"\", \"component\": \"\", \"extraIcon\": \"\", \"hiddenTag\": false, \"keepAlive\": true, \"activePath\": \"\", \"showParent\": true, \"frameLoading\": true, \"enterTransition\": \"\"}', 1, '2024-05-15 08:08:32', '2024-05-15 08:59:44');");
        DB::statement("INSERT INTO {$tableName} (`id`, `guard_name`, `parent_id`, `type`, `title`, `name`, `path`, `sort`, `meta`, `is_show`, `created_at`, `updated_at`) VALUES (11, 'dashboard', 8, 'link', 'Pure Admin Utils', 'https://pure-admin-utils.netlify.app/', '/pureUtilsLink', 99, '{\"icon\": \"\", \"fixedTag\": false, \"frameSrc\": \"\", \"redirect\": \"\", \"component\": \"\", \"extraIcon\": \"\", \"hiddenTag\": false, \"keepAlive\": false, \"activePath\": \"\", \"showParent\": true, \"frameLoading\": true, \"enterTransition\": \"\"}', 1, '2024-05-15 08:09:39', '2024-05-15 08:59:53');");
    }
}
