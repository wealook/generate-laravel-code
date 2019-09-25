<?php

namespace Wealook\Generate\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProduceModel extends GeneratorCommand
{
    protected $signature = 'produce:model {name} {--table=}  {--connection=} {--force}';
    protected $description = 'Command description';
    protected $type = '';

    protected $tableInfo = [];

    protected $table = '';
    protected $connection = '';

    protected $nameSpace = '';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $this->table = $this->option('table');
        $this->connection = $this->option('connection') ?? config('database.default');
        if (!$this->table) {
            $this->error('DB table must input  --table=table_name');
            return false;
        }
        $this->getTableInfo();
        $this->buildModel();
        return null;
    }


    private function buildController()
    {
        $this->setNamespace('\Http\Controllers');
        $this->type = 'controller';
        $name = $this->qualifyClass($this->getNameInput()) . ucfirst($this->type);
        $path = lcfirst($this->getPath($name));
//        . lcfirst($this->type);
        if ((!$this->hasOption('force') || !$this->option('force')) && $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type . ' already exists!');
            return false;
        }

        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));
        $this->info($this->type . ' created successfully.');
    }

    private function buildModel()
    {
        $this->setNamespace('\Models');
        $this->type = '';
        $name = $this->qualifyClass($this->getNameInput()) . ucfirst($this->type);
        $path = $this->getPath($name);
        if ((!$this->hasOption('force') || !$this->option('force')) && $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type . ' already exists!');
            return false;
        }
        $this->type = 'Model';

        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));
        $this->info($this->type . ' created successfully.');
    }

    private function buildService()
    {
        $this->setNamespace('\Services');
        $this->type = 'Service';

        $name = $this->qualifyClass($this->getNameInput()) . ucfirst($this->type);
        $path = $this->getPath($name);
        if ((!$this->hasOption('force') || !$this->option('force')) && $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type . ' already exists!');
            return false;
        }
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));
        $this->info($this->type . ' created successfully.');
    }

    private function buildRepository()
    {
        $this->setNamespace('\Repositories');
        $this->type = 'Repository';
        $name = $this->qualifyClass($this->getNameInput()) . ucfirst($this->type);
        $path = $this->getPath($name);
        if ((!$this->hasOption('force') || !$this->option('force')) && $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type . ' already exists!');
            return false;
        }
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));
        $this->info($this->type . ' created successfully.');
    }

    public function buildRoute()
    {
//        $this->setNamespace('\Http\Controllers');
        $this->type = 'controller';
        $name = $this->getNameInput() . ucfirst($this->type);

        $routes = [];

        $tmpArr = explode('\\', str_replace(['//', '/'], ['\\', '\\'], $this->getNameInput()));
        $routePrefix = [];
        foreach ($tmpArr as $str) {
            $routePrefix[] = Str::snake($str);
        }
        $tmpPrefix = implode(".", $routePrefix);
        if ($this->option('resource')) {
            $routes[] = "Route::resource('$tmpPrefix','$name');";
        } else {
            $tmpStr = implode(".", $routePrefix);
            $routes[] = "Route::get('$tmpPrefix','$name@index')->name('$tmpStr.index');";
            $routes[] = "Route::post('$tmpPrefix/store','$name@store')->name('$tmpStr.store');";
            $routes[] = "Route::post('$tmpPrefix/update','$name@update')->name('$tmpStr.update');";
            $routes[] = "Route::post('$tmpPrefix/destroy','$name@destroy')->name('$tmpStr.destroy');";
        }
        $routeFilePath = base_path('routes/' . $this->option('route') . '.php');
        if (!$this->files->exists($routeFilePath)) {
            $this->info('route file not exists');
            return null;
        }
        $content = implode("\n", $routes);
        $this->files->append($routeFilePath, "\n" . $content);
    }

    protected function setNamespace($nameSpace)
    {
        $this->nameSpace = $nameSpace;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . $this->nameSpace;
        //. '\Http\Controllers';
    }

    protected function getStub()
    {
        return __DIR__ . '/../stubs/' . $this->type . '.stub';
    }

    public function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        Log::info($stub);
        $method = 'replace' . lcfirst($this->type);
        $this->$method($stub, $name);
//        $this->type = '';
        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    public function replaceController(&$stub, $name)
    {
        $rules = [];
        $messages = [];
        foreach ($this->tableInfo as $item) {
            if ($item->Key == 'PRI') {
                continue;
            }
            if (in_array($item->Field, ['created_at', 'updated_at'])) {
                continue;
            }
            $arr = [];
            $rule = [];
            $message = [];
            $paramName = $item->Field;

            if ($item->Null == 'NO') {
                $rule[] = 'required';
                $message[$paramName . '.required'] = '';
            }
            if (preg_match('/char\((?<number>[0-9]*?)\)/', $item->Type, $arr)) {
                $rule[] = 'string';
                $message[$paramName . '.string'] = '';
                $rule[] = 'max:' . $arr['number'];
                $message[$paramName . '.max'] = '';
            }
            if (preg_match('/int/', $item->Type, $arr)) {
                $rule[] = 'integer';
                $message[$paramName . '.integer'] = '';
            }
            if (in_array($item->Type, ['timestamp', 'datetime'])) {
                $rule[] = 'date';
                $message[$paramName . '.date'] = '';
            }
            $message[$paramName . '.*'] = '';

            $rules[] = "'$paramName'=>'" . implode('|', $rule) . "',";
            foreach ($message as $messageKey => $messageItem) {
                $messages[] = "'$messageKey'=>'$messageItem',";
            }
        }
        $tmpArr = explode("\\", $this->getNameInput());
        $lastName = array_pop($tmpArr);
        $simpleNamespace = implode('\\', $tmpArr);
        $stub = str_replace(
            [
                'DummySimpleNamespace', 'DummyServiceClass', 'DummyServiceVariable',
                'DummyStoreValidateVariables', 'DummyStoreValidateMessages',
                'DummyUpdateValidateVariables', 'DummyUpdateValidateMessages'
            ],
            [
                $simpleNamespace, ucfirst($lastName . 'Service'), lcfirst($lastName . 'Service'),
                implode("\n", $rules), implode("\n", $messages),
                implode("\n", $rules), implode("\n", $messages),
            ],
            $stub
        );
        return $this;
    }


    public function replaceModel(&$stub, $name)
    {
        $fillable = [];
        $hidden = [];
        $timestamps = false;
        foreach ($this->tableInfo as $item) {
            if ($item->Key == 'PRI') {
                continue;
            }
//            if (in_array($item->Field, ['created_at', 'updated_at'])) {
//                continue;
//            }
            $fillable[] = "'" . $item->Field . "'";
            if (preg_match('/password/', $item->Field)) {
                $hidden[] = "'" . $item->Field . "'";
            }
        }
        if (isset($fillable['created_at']) && isset($fillable['updated_at'])) {
            $timestamps = true;
        }
        $stub = str_replace(
            [
                'DummyModelFillable', 'DummyModelHidden',
                'DummyModelConnection', 'DummyModelTable', 'DummyModelTimestamps'
            ],
            [
                implode(', ', $fillable), implode(', ', $hidden),
                $this->connection, $this->table, $timestamps
            ],
            $stub
        );
        return $this;
    }


    public function replaceService(&$stub, $name)
    {
        $fillFields = [];
        foreach ($this->tableInfo as $item) {
            if ($item->Key == 'PRI') {
                continue;
            }
            if (in_array($item->Field, ['updated_at', 'created_at'])) {
                continue;
            }
            if (strpos($item->Field, 'password') > 0) {
                continue;
            }
            $key = $item->Field;
            $fillFields[] = "'$key'=>\$request['$key'],";
        }
        $tmpArr = explode("\\", $this->getNameInput());
        $lastName = array_pop($tmpArr);
        $simpleNamespace = implode('\\', $tmpArr);
        $stub = str_replace(
            [
                'DummySimpleNamespace', 'DummyRepositoryClass', 'DummyRepositoryVariable', 'DummyModelClass', 'DummyModelVariable',
                'DummyModelStoreFields', 'DummyModelUpdateFields'
            ],
            [
                $simpleNamespace, ucfirst($lastName . 'Repository'), lcfirst($lastName . 'Repository'), ucfirst($lastName), lcfirst($lastName),
                implode("\n", $fillFields), implode("\n", $fillFields),
            ],
            $stub
        );
        return $this;

    }

    public function replaceRepository(&$stub, $name)
    {
        $filterCodes = [];
        foreach ($this->tableInfo as $item) {
            if ($item->Key == 'PRI') {
                continue;
            }
            if (in_array($item->Field, ['updated_at'])) {
                continue;
            }
            if (strpos($item->Field, 'password') > 0) {
                continue;
            }
            if (preg_match('/^varchar/', $item->Type)) {
                $key = $item->Field;
                $code = "
                    if(!empty(\$filter['$key'])){
                       \$DummyModelVariable->where('$key','like','%'.\$filter['$key'].'%');
                    }";
            } else {
                if (in_array($item->Type, ['timestamp', 'datetime'])) {
                    $key = $item->Field . '_start';
                    $code = "
                    if(!empty(\$filter['$key'])){
                       \$DummyModelVariable->where('$key','>=',\$filter['$key']);
                    }";
                    $key = $item->Field . '_end';
                    $code .= "\n
                    if(!empty(\$filter['$key'])){
                       \$DummyModelVariable->where('$key','<=',\$filter['$key']);
                    }";
                } else {
                    $key = $item->Field;
                    $code = "
                    if(!empty(\$filter['$key'])){
                       \$DummyModelVariable->where('$key',\$filter['$key']);
                    }";
                }
            }
            $filterCodes[] = $code;

        }
        $tmpArr = explode("\\", $this->getNameInput());
        $lastName = array_pop($tmpArr);
        $simpleNamespace = implode('\\', $tmpArr);
        $stub = str_replace(
            ['DummySimpleNamespace', 'DummyFilterCode', 'DummyModelClass', 'DummyModelVariable'],
            [$simpleNamespace, implode("\n", $filterCodes), ucfirst($lastName), lcfirst($lastName)],
            $stub
        );
        return $this;
    }

    public function makeRoute($path, $name)
    {

    }

    private function getTableInfo()
    {
        $info = DB::connection($this->connection)->select('desc  ' . $this->table);
        $this->tableInfo = $info;
    }

}
