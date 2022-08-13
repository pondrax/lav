<?php

namespace App\Http\Controllers\App;

use App\Http\Requests\App\AssignRolePermissionRequest;
use App\Http\Requests\App\StoreRoleRequest;
use App\Http\Requests\App\UpdateRoleRequest;
use App\Http\Resources\Response;
use App\Models\Permission;
use App\Models\Role;

/**
 * @group #Role Management
 * @authenticated
 */
class RoleController extends Controller
{
    /**
     * List Role
     *
     * List all available Roles
     */
    public function index(Role $role)
    {
        return new Response($role->with('permissions')->filter()->table());
    }

    /**
     * Show Role Detail
     *
     * Show Detail for specific role
     */
    public function show(Role $role)
    {
        return new Response($role);
    }

    /**
     * Save Role
     */
    public function store(Role $role, StoreRoleRequest $request)
    {
        $data = $request->validated();

        return new Response($role->firstOrCreate($data), 201, 'Added');
    }

    /**
     * Assign Role Permission
     */
    public function assignPermission(Role $role, AssignRolePermissionRequest $request)
    {
        $permissions = $request->validated()['permissions'];
        if (Permission::whereIn('slug', $permissions)->count() > 0) {
            $role->revokeAllPermissions();
        }
        collect($permissions)->each(function ($permission) use (&$role) {
            $role->grantPermissionBySlug($permission);
        });

        return new Response($role, 200, 'Saved');
    }

    /**
     * Update Role
     */
    public function update(Role $role, UpdateRoleRequest $request)
    {
        $data = $request->validated();

        return new Response($role->update($data), 200, 'Updated');
    }

    /**
     * Delete Role
     */
    public function destroy(Role $role)
    {
        return new Response($role, 200, 'Deleted');
    }
}
