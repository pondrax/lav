<?php

return [
    /**
     * User class used for ACL.
     */
    'user' => App\Models\User::class,

    /**
     * Role class used for ACL.
     */
    'role' => App\Models\Role::class,
    // 'role' => Yajra\Acl\Models\Role::class,

    /**
     * Permission class used for ACL.
     */
    'permission' => App\Models\Permission::class,
    // 'permission' => Yajra\Acl\Models\Permission::class,

    /**
     * Cache config.
     */
    'cache' => [
        'enabled' => true,

        'key' => 'permissions.policies',
    ],
];
