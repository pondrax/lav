<?php

namespace Database\Seeders;

class RoleUserSeeder extends BaseSeeder
{
    protected $model = \App\Models\User::class;

    protected $filePath = 'database/csv/role_user.csv';

    protected $pivot = 'roles';

    public function run()
    {
        parent::run();
    }
}
