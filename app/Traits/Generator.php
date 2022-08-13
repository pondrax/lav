<?php

namespace App\Traits;

use App\Models\Permission;
use Bpocallaghan\Generators\Migrations\SchemaParser;
use Brick\VarExporter\VarExporter;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class Generator
{
    protected $generated = [
        'model' => null,
        'migration' => null,
        'controller' => null,
        'request' => [],
    ];

    public function generate($name, $methods = [], $schema = null, $migrate = false, $force = false)
    {
        chdir(base_path()); // Set artisan directory at base_path
        $resourceName = str($name)->afterLast('/')->ucfirst();
        if (in_array('store', $methods)) {
            $messages[] = $this->generateRequest('Store'.$resourceName, $schema, $force);
        }
        if (in_array('update', $methods)) {
            $messages[] = $this->generateRequest('Update'.$resourceName, $schema, $force);
        }
        if ($force) { //remove similar migration
            $migrationName = 'create_'.str($name)->afterLast('/').'s_table.php';
            collect(File::glob(base_path("database/migrations/*$migrationName")))->each(function ($item) {
                unlink($item);
            });
        }
        $messages[] = $this->generateModel($name, $schema, $force);
        $messages[] = $this->generateController($name, $methods, $force);
        $messages[] = $this->generateRoute($name, $methods);
        $messages[] = $this->generatePermission($name, $methods);
        // $messages[] = $this->migrate($migrate);

        return [$this->generated, $messages];
    }

    public function generateController($name, $methods = [], $force = false)
    {
        Artisan::call('generate:controller', [
            'name' => $name,
            '--force' => $force,
            '--stub' => 'controller_api',
        ]);
        $out = str(Artisan::output())->replace('- ./', '');
        if ($out->contains('app')) {
            $this->generated['controller'] = $out->explode(PHP_EOL)->get(1);
        }

        return $out;
    }

    public function generateRequest($name, $schema = null, $force = false)
    {
        Artisan::call('generate:request', [
            'name' => str($name)->camel()->ucfirst(),
            '--force' => $force,
        ]);
        $out = str(Artisan::output())->replace('- ./', '');
        if ($out->contains('app')) {
            $file = $out->explode(PHP_EOL)->get(1);
            $content = str(file_get_contents($file));
            $rules = $this->parseSchemaRules($schema);
            file_put_contents($file, $content->replace('{{rules}}', $rules));

            $this->generated['request'][] = $file;
        }

        return $out;
    }

    public function generateModel($name, $schema, $force)
    {
        Artisan::call('generate:model', [
            'name' => $name,
            '--migration' => true,
            '--schema' => $schema,
            '--force' => $force,
        ]);
        $out = str(Artisan::output())->replace('- ./', '');
        if ($out->contains('app')) {
            $file = $out->explode(PHP_EOL)->get(1);
            $content = str(file_get_contents($file));
            $fillable = $this->parseSchemaFillable($schema);
            file_put_contents($file, $content->replace('{{fillable}}', $fillable));

            $this->generated['model'] = $file;
            $this->generated['migration'] = $out->explode(PHP_EOL)->get(3);
        }

        return $out;
    }

    public function generateRoute($name, $methods = [])
    {
        $routePath = base_path('routes/api.php');

        $generated = implode('', [
            // PHP_EOL,
            "Route::apiResource('$name', ".$this->fileToNamespace($this->generated['controller']).'::class)',
            // PHP_EOL."\t",
            "->only(['".implode("','", $methods)."'])",
            // PHP_EOL."\t",
            "->middleware(['auth:sanctum','route.permission']);",
            ' #generated#',
        ]);

        $routeTxt = '';
        $file = new \SplFileObject($routePath);
        $exist = false;
        while (! $file->eof()) {
            $line = str($file->fgets());
            if ($line->contains('#generated') && $line->contains($name)) {
                $exist = true;
                $routeTxt .= $generated;
            } else {
                $routeTxt .= $line;
            }
        }
        if (! $exist) {
            $routeTxt .= PHP_EOL.$generated;
        }
        file_put_contents($routePath, $routeTxt);

        return "Route added successfully.\r\n";
    }

    public function migrate()
    {
        Artisan::call('migrate');

        return Artisan::output();
    }
    public function generatePermission($name, $methods=[])
    {
        $name = str($name)->afterLast('/');
        collect($methods)->each(function($method) use($name){
            Permission::firstOrCreate([
                'name' =>  $name->ucfirst() . ' ' . $method,
                'slug'  => $name . '.' . $method
            ])->attachRoleBySlug('developer');
        });
        return 'Permissions Generated, Assigned to Role Developer';
    }

    public function generateDocs()
    {
        Artisan::call('scribe:generate', ['--force']);

        return Artisan::output();
    }

    public function parseSchema($schema)
    {
        $default = collect([
            ['name' => 'id', 'type' => 'uuid', 'arguments' => [], 'options' => ['primary' => true]],
            ['name' => 'created_at', 'type' => 'datetime', 'arguments' => [], 'options' => []],
            ['name' => 'updated_at', 'type' => 'datetime', 'arguments' => [], 'options' => []],
        ]);
        $schema = $default->merge((new SchemaParser)->parse($schema))->unique();

        return $schema;
    }

    public function parseSchemaRules($schema)
    {
        $rules = $this->parseSchema($schema)->filter(function ($item) {
            return ! in_array($item['name'], ['id', 'created_at', 'updated_at']);
        })->mapWithKeys(function ($item) {
            $option = collect([
                isset($item['options']['nullable']) ? '' : 'required',
                $item['type'],
            ])->filter();

            return [
                $item['name'] => $option->values()->all(),
            ];
        })->all();

        return VarExporter::export($rules, indentLevel: 2);
    }

    public function parseSchemaFillable($schema)
    {
        $fillable = $this->parseSchema($schema)->filter(function ($item) {
            return ! in_array($item['name'], ['id', 'created_at', 'updated_at']);
        })->map(function ($item) {
            return $item['name'];
        })->values()->all();

        return VarExporter::export($fillable, indentLevel: 1);
    }

    public function fileToNamespace($filePath)
    {
        return str($filePath)->replace('/', '\\')->replace('.php', '')->ucfirst();
    }
}
