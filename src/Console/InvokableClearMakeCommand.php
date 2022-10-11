<?php

namespace BiiiiiigMonster\Clearable\Console;

use Illuminate\Console\GeneratorCommand;

class InvokableClearMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:clear';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     */
    protected static $defaultName = 'make:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new clear attribute';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Clear';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $relativePath = '/stubs/invoke-clear.stub';

        return file_exists($customPath = $this->laravel->basePath(trim($relativePath, '/')))
            ? $customPath
            : __DIR__.$relativePath;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Clears';
    }
}
