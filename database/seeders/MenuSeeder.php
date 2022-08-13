<?php

namespace Database\Seeders;

class MenuSeeder extends BaseSeeder
{
    protected $model = \App\Models\Menu::class;

    protected $filePath = 'database/csv/menu.csv';

    public function run()
    {
        parent::run();
    }
}
