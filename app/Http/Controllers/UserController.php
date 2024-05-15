<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function profile()
    {
        $userData = auth()->user();

        return $this->success($userData);
    }

    public function asyncRoutes()
    {
        $routes = User::getMenusWithHierarchy(auth()->user());

        return $this->success($routes);
    }
}
