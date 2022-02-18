<?php


namespace BiiiiiigMonster\Cleans;


use BiiiiiigMonster\Cleans\Console\CleansAttributesMakeCommand;
use Illuminate\Support\ServiceProvider;

class CleansServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(CleansAttributesMakeCommand::class);
    }
}