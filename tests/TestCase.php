<?php

namespace Tests;

use GeTracker\BasicActivityLog\ActivityLogSupervisor;
use GeTracker\BasicActivityLog\BasicActivityLogServiceProvider;
use GeTracker\BasicActivityLog\Handlers\ActivityLogHandlerInterface;
use GeTracker\BasicActivityLog\Handlers\EloquentHandler;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [BasicActivityLogServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
