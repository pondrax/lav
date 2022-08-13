<?php

namespace Database\Seeders;

class RoleSeeder extends BaseSeeder
{
    protected $model = \App\Models\Role::class;

    protected $filePath = 'database/csv/role.csv';

    public function run()
    {
        parent::run();
    }
}
