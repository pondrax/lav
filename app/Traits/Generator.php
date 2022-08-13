<?php

namespace App\Traits;

use App\Models\Permission;
use Bpocallaghan\Generators\Migrations\SchemaParser;
use Brick\VarExporter\VarExporter;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class Generator
{
    protected $generated;

    public function __construct()
    {
        $this->generated = collect([]);
    }
    public function generate($name, $methods = [], $schema = null, $migrate = false, $force = false)
    {
        chdir(base_path()); // Set artisan directory at base_path
        if (in_array('store', $methods)) {
            $this->generateRequest('Store_'.$name, $schema, $force);
        }
        if (in_array('update', $methods)) {
            $this->generateRequest('Update_'.$name, $schema, $force);
        }
        $this->cleanMigration($name, $force);
        $this->generateModel($name, $schema, $force);
        $this->generateController($name, $methods, $force);
        $this->generateRoute($name, $methods);
        $this->generatePermission($name, $methods);
        $this->migrate($force);

        $messages = $this->generated->map(fn($i)=>$i['message']);

        return [$this->generated, $messages];
    }

    public function cleanMigration($name, $force)
    {
        if ($force) { //remove similar migration
            $migrationName = 'create_'.str($name)->afterLast('/').'s_table.php';
            collect(File::glob(base_path("database/migrations/*$migrationName")))->each(function ($item) {
                unlink($item);
            });
        }
    }
    public function getStub($methods)
    {
        $methods = collect($methods);
        if($methods->contains('store') && $methods->contains('update')){
            return 'controller_api_all';
        }elseif($methods->contains('store')){
            return 'controller_api_store';
        }elseif($methods->contains('update')){
            return 'controller_api_update';
        }
        return 'controller_api';
    }

    public function generateController($name, $methods = [], $force = false)
    {        
        Artisan::call('generate:controller', [
            'name' => $name,
            '--force' => $force,
            '--stub' => $this->getStub($methods),
        ]);
        $out = str(Artisan::output())->replace('- ./', '');
        if ($out->contains('app')) {
            [$msg,$file] = $out->explode(PHP_EOL);
            $this->generated->push([
                'type'  => 'controller',
                'file'  => $file,
                'message' => $msg,
                'props' =>$this->fileToNamespace($file)
            ]);
        }
    }

    public function generateRequest($name, $schema = null, $force = false)
    {
        Artisan::call('generate:request', [
            'name' => str($name)->camel()->ucfirst(),
            '--force' => $force,
        ]);
        $out = str(Artisan::output())->replace('- ./', '');
        
        if ($out->contains('app')) {
            [$msg,$file] = $out->explode(PHP_EOL);
            $rules = $this->parseSchemaRules($name, $schema);
            $this->generated->push([
                'type'  => 'request',
                'file'  => $file,
                'message' => $msg,
                'props' => $rules
            ]);

            File::replaceInFile('{{rules}}', VarExporter::export($rules, indentLevel:2) , $file);
        }
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
            [$modelMsg,$model,$migrationMsg, $migration] = $out->explode(PHP_EOL);
            $fillable = $this->parseSchemaFillable($schema);
            $this->generated->push([
                'type'  => 'model',
                'file'  => $model,
                'message' => $modelMsg,
                'props' => compact('fillable')
            ])->push([
                'type'  => 'migration',
                'file'  => $migration,
                'message' => $migrationMsg,
                'props' => null
            ]);
            File::replaceInFile('{{fillable}}', VarExporter::export($fillable, indentLevel:1), $model);
        }

    }

    public function generateRoute($name, $methods = [])
    {
        $action = $this->generated->where('type','controller')->first()['props'];
        $generated = implode('', [
            // PHP_EOL,
            "Route::apiResource('$name', $action::class)",
            "->only(['".implode("','", $methods)."'])",
            "->middleware(['auth:sanctum','route.permission']);",
            ' #generated#',
        ]);
        
        $path = base_path('routes/api.php');
        $txt = File::get($path);
        $pattern = "/(^Route.*\'$name\'.*\#generated\#$)/im";
        if(preg_match($pattern, $txt)){
            $txt = preg_replace($pattern, $generated, $txt);
        }else{
            $txt.= PHP_EOL . $generated;
        }
        File::put($path,$txt);
        $this->generated->push([
            'type'  => 'route',
            'file'  => $path,
            'message' => 'Route added successfully',
            'props' => $generated
        ]);
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
                'slug'  => $name . '.' . $method,
                'system' => 1
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

    public function parseSchemaRules($name, $schema)
    {
        $rules = $this->parseSchema($schema)->filter(function ($item) {
            return ! in_array($item['name'], ['id', 'created_at', 'updated_at']);
        })->mapWithKeys(function ($item) use($name){
            $validators = ['string','date','integer','uuid'];
            $option = collect([
                isset($item['options']['nullable']) ? '' : 'required',
                isset($item['options']['unique']) ? 'unique:'. str($name)->afterLast('_')->afterLast('/') . 's' : '',
                in_array($item['type'], $validators)?$item['type']: 'string',
            ])->filter();

            return [
                $item['name'] => $option->values()->all(),
            ];
        })->all();

        return $rules;
    }

    public function parseSchemaFillable($schema)
    {
        $fillable = $this->parseSchema($schema)->filter(function ($item) {
            return ! in_array($item['name'], ['id', 'created_at', 'updated_at']);
        })->map(function ($item) {
            return $item['name'];
        })->values()->all();

        return $fillable;
    }

    public function fileToNamespace($filePath)
    {
        return str($filePath)->replace('/', '\\')->replace('.php', '')->ucfirst();
    }
}
