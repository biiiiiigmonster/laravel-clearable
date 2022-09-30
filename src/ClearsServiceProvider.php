<?php

namespace BiiiiiigMonster\Clearable;

use BiiiiiigMonster\Clearable\Console\ClearsAttributesMakeCommand;
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
