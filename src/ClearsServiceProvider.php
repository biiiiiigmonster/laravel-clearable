<?php

namespace BiiiiiigMonster\Clears;

use BiiiiiigMonster\Clears\Console\ClearsAttributesMakeCommand;
use Illuminate\Support\ServiceProvider;

class ClearsServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(ClearsAttributesMakeCommand::class);
    }
}
