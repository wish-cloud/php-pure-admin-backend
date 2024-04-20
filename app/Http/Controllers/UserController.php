<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function asyncRoutes()
    {
        $routes = [
            [
                'path' => '/permission',
                'meta' => [
                    'title' => '权限管理',
                    'icon' => 'ep:lollipop',
                    'rank' => 10,
                ],
                'children' => [
                    [
                        'path' => '/permission/page/index',
                        'name' => 'PermissionPage',
                        'meta' => [
                            'title' => '页面权限',
                            'roles' => ['admin', 'common'],
                        ],
                    ],
                    [
                        'path' => '/permission/button/index',
                        'name' => 'PermissionButton',
                        'meta' => [
                            'title' => '按钮权限',
                            'roles' => ['admin', 'common'],
                            'auths' => [
                                'permission:btn:add',
                                'permission:btn:edit',
                                'permission:btn:delete',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this->success($routes);
    }
}
