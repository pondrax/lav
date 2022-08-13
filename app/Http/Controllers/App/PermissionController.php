<?php

namespace App\Http\Controllers\App;

use App\Http\Resources\Response;
use App\Models\Permission;

/**
 * @group #Role Management
 * @authenticated
 */
class PermissionController extends Controller
{
    /**
     * List All Permission
     */
    public function index(Permission $permission)
    {
        return new Response($permission->filter()->table());
    }

    /**
     * Show Permission Detail
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');

        return new Response($permission);
    }
}
