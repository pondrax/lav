<?php

namespace Database\Seeders;

class PermissionRoleSeeder extends BaseSeeder
{
    protected $model = \App\Models\Role::class;

    protected $filePath = 'database/csv/permission_role.csv';

    protected $pivot = 'permissions';

    public function run()
    {
        parent::run();
    }
}
