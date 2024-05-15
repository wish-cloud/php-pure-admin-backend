<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Manager\AccountController;
use App\Http\Controllers\Manager\MenuController;
use App\Http\Controllers\Manager\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckApiToken;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

Route::group([
    'middleware' => ['auth:sanctum', CheckApiToken::class],
], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [UserController::class, 'profile']);
    Route::get('async-routes', [UserController::class, 'asyncRoutes']);
});

Route::group([
    'prefix' => 'manager',
    'middleware' => ['auth:sanctum', CheckApiToken::class, 'can:manager'],
], function () {
    Route::get('users', [AccountController::class, 'list']);
    Route::post('user/create', [AccountController::class, 'create']);
    Route::post('user/edit', [AccountController::class, 'edit']);
    Route::post('user/changePassword', [AccountController::class, 'changePassword']);
    Route::post('user/changeStatus', [AccountController::class, 'changeStatus']);
    Route::post('user/delete', [AccountController::class, 'delete']);
    Route::post('user/batchDelete', [AccountController::class, 'batchDelete']);
    Route::post('user/assignRole', [AccountController::class, 'assignRole']);
    Route::get('roles', [RoleController::class, 'list']);
    Route::get('all-roles', [RoleController::class, 'all']);
    Route::post('role/create', [RoleController::class, 'create']);
    Route::post('role/edit', [RoleController::class, 'edit']);
    Route::post('role/delete', [RoleController::class, 'delete']);
    Route::post('role/setMenu', [RoleController::class, 'setMenu']);
    Route::get('role-menu-ids', [RoleController::class, 'menuIds']);
    Route::get('menus', [MenuController::class, 'list']);
    Route::post('menu/create', [MenuController::class, 'create']);
    Route::post('menu/edit', [MenuController::class, 'edit']);
    Route::post('menu/delete', [MenuController::class, 'delete']);
});
