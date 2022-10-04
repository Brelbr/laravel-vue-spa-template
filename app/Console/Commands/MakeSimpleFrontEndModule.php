<?php

namespace App\Console\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Output\ConsoleOutput;

class MakeSimpleFrontEndModule extends MakeFrontEndModuleCommand
{
    /**
     * MakeFrontEndModule constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->output = new ConsoleOutput();
    }

    /**
     * @var string
     */
    private $module_path;

    /**
     * @param $module
     * @throws FileNotFoundException
     */
    protected function create($module) {
        $this->files = new Filesystem();
        $this->module = $module;
        $this->module_path = base_path('resources/js/modules/'.lcfirst($this->module));

        $this->createVueForm();

        $this->createRoutes();

    }

    /**
     * Create a Vue component file for the module.
     *
     * @return void
     * @throws FileNotFoundException
     */
    private function createVueForm()
    {
        $path = $this->module_path."/components/{$this->module}.vue";

        if ($this->alreadyExists($path)) {
            $this->error('VueList Component already exists!');
        } else {
            $stub = $this->files->get(base_path('stubs/frontEnd/vue.simpleform.stub'));

            $this->createFileWithStub($stub, $path);

            $this->info('VueList Component created successfully.');
        }
    }

    /**
     * Create a Vue component file for the module.
     *
     * @return void
     * @throws FileNotFoundException
     */
    private function createRoutes()
    {
        $path = $this->module_path.'/routes.js';

        if ($this->alreadyExists($path)) {
            $this->error('Vue Routes already exists!');
        } else {
            $stub = $this->files->get(base_path('stubs/frontEnd/simpleformroutes.stub'));

            $this->createFileWithStub($stub, $path);

            $this->info('Vue Routes created successfully.');
        }
    }
}
