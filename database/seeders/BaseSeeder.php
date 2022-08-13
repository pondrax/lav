<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaseSeeder extends Seeder
{
    protected $model;

    protected $filePath;

    protected $delimiter = ',';

    protected $truncate = false;

    protected $pivot = null;

    public function run()
    {
        $model = new $this->model;
        if ($this->truncate) {
            $model->truncate();
        }
        $data = [];
        if (($handle = fopen(base_path($this->filePath), 'r')) !== false) {
            if (($row = fgetcsv($handle, 1000, $this->delimiter)) !== false) {
                $keys = $row;
            }
            while (($row = fgetcsv($handle, 1000, $this->delimiter)) !== false) {
                if ($this->pivot) {
                    $data = collect($keys)->map(function ($k, $i) use ($row) {
                        [$table, $column] = explode('.', $k);

                        return [
                            'table' => $table,
                            'where' => [$column => $row[$i]],
                        ];
                    })->all();

                    if ($data[0]['table'] != $model->getTable()) {
                        $data = array_reverse($data);
                    }
                    [$local, $pivot] = $data;

                    $pivotId = DB::table($pivot['table'])->where($pivot['where'])->value('id');

                    $model->firstWhere($local['where'])
            ->{$this->pivot}()
            ->attach($pivotId);
                } else {
                    $data = collect($keys)->mapWithKeys(function ($k, $i) use ($row) {
                        $key = $k;
                        $val = $row[$i];
                        if (str($k)->contains('|')) {
                            [$key, $format] = explode('|', $key);
                            if ($format == 'json') {
                                $val = json_decode(str_replace("'", '"', $val));
                                // var_dump($val);
                            }
                        }

                        return [$key => $val];
                    })->all();
                    // var_dump($data, $keys);
                    $model->create($data);
                }
            }
            fclose($handle);
        }
    }
}
