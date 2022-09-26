<?php

namespace BiiiiiigMonster\Clears\Tests;

use BiiiiiigMonster\Clears\ClearsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ClearsServiceProvider::class,
        ];
    }
}
