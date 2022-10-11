<?php

namespace BiiiiiigMonster\Clearable;

use BiiiiiigMonster\Clearable\Console\InvokableClearMakeCommand;
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
        $this->commands(InvokableClearMakeCommand::class);
    }
}
