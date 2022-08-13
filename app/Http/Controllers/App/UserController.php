<?php

namespace App\Http\Controllers\App;

use App\Http\Requests\App\AssignUserRoleRequest;
use App\Http\Requests\App\StoreUserRequest;
use App\Http\Requests\App\UpdateUserRequest;
use App\Http\Resources\Response;
use App\Models\Role;
use App\Models\User;

/**
 * @group #User Management
 *
 * @authenticated
 */
class UserController extends Controller
{
    /**
     * List User
     *
     * List All User
     */
    public function index(User $user)
    {
        return new Response($user->filter()->table());
    }

    /**
     * Show Detail User
     *
     * Show Detail User
     */
    public function show(User $user)
    {
        return new Response($user);
    }

    /**
     * Save User
     *
     * Save User
     */
    public function store(User $user, StoreUserRequest $request)
    {
        $data = $request->validated();
        $user = $user->create($data);
        $user->attachRoleBySlug('member');

        return new Response($user, 200, 'Updated');
    }

    /**
     * Attach Role to User
     *
     * Attach Role to user
     */
    public function assignRole(User $user, AssignUserRoleRequest $request)
    {
        $roles = $request->validated()['roles'];
        if (Role::whereIn('slug', $roles)->count() > 0) {
            $user->revokeAllRoles();
        }
        collect($roles)->each(function ($role) use (&$user) {
            $user->attachRoleBySlug($role);
        });

        return new Response($user, 200, 'Updated');
    }

    /**
     * Update User
     *
     * Update Detail User
     */
    public function update(User $user, UpdateUserRequest $request)
    {
        $data = $request->validated();
        $user->update($data);

        return new Response($user, 200, 'Updated');
    }

    /**
     * Delete User
     *
     * Delete User
     */
    public function destroy(User $user)
    {
        $user->delete();

        return new Response($user, 200, 'Deleted');
    }
}
