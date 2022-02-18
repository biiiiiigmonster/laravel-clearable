<?php

namespace BiiiiiigMonster\Cleans\Console;

use Illuminate\Console\GeneratorCommand;

class CleanAttributesMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:clean';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     */
    protected static $defaultName = 'make:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new clean attribute';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Clean';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $relativePath = '/stubs/clean-attribute.stub';

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
        return $rootNamespace.'\Cleans';
    }
}
