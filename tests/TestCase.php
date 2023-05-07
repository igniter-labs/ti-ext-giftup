<?php

namespace IgniterLabs\GiftUp\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Igniter\Flame\ServiceProvider::class,
            \IgniterLabs\GiftUp\Extension::class,
        ];
    }
}
