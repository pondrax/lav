<?php

namespace Database\Seeders;

use App\Models\User;

class UserSeeder extends BaseSeeder
{
    protected $model = User::class;

    protected $filePath = 'database/csv/users.csv';

    public function run()
    {
        parent::run();
    }
}
