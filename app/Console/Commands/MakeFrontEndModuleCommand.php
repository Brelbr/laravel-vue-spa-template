<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class MakeFrontEndModuleCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:frontendmodule {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Module for front-end';

    /** @var Stringable */
    protected $module;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param Filesystem $files
     * @param MakeSimpleFrontEndModule $frontEndModule
     * @return void
     * @throws FileNotFoundException
     */
    public function handle(Filesystem $files, MakeSimpleFrontEndModule $frontEndModule)
    {
        $this->files = $files;

        $this->module = Str::of(class_basename($this->argument('name')))->studly()->singular();

        $frontEndModule->create($this->module);

    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $path
     * @return bool
     */
    protected function alreadyExists($path)
    {
        return $this->files->exists($path);
    }

    /**
     * @param $stub
     * @param $path
     * @return void
     */
    protected function createFileWithStub($stub, $path)
    {
        $this->makeDirectory($path);

        $content = str_replace([
            'DummyRootNamespace',
            'DummySingular',
            'DummyPlural',
            'DUMMY_VARIABLE_SINGULAR',
            'DUMMY_VARIABLE_PLURAL',
            'dummyVariableSingular',
            'dummyVariablePlural',
            'dummy-plural',
            'dummyVariableSnakePlural',
        ], [
            App::getNamespace(),
            $this->module,
            $this->module->pluralStudly(),
            $this->module->snake()->upper(),
            $this->module->plural()->snake()->upper(),
            lcfirst($this->module),
            lcfirst($this->module->pluralStudly()),
            lcfirst($this->module->plural()->snake('-')),
            $this->module->plural()->snake(),
        ],
            $stub
        );

        $this->files->put($path, $content);
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }
}
