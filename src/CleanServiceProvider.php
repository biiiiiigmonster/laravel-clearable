<?php


namespace BiiiiiigMonster\Cleans;


use BiiiiiigMonster\Cleans\Console\CleanAttributesMakeCommand;
use Illuminate\Support\ServiceProvider;

class CleanServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(CleanAttributesMakeCommand::class);
    }
}