<?php

namespace Database\Seeders;

class PermissionSeeder extends BaseSeeder
{
    protected $model = \App\Models\Permission::class;

    protected $filePath = 'database/csv/permission.csv';

    public function run()
    {
        parent::run();
    }
}
