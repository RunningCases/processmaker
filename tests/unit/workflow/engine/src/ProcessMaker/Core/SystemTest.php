<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Core;

use ProcessMaker\Core\System;
use Tests\TestCase;

class SystemTest extends TestCase
{
    /**
     * Define the required variables
     */
    protected function setUp()
    {
        $config = config('database.connections.testexternal');
        define('DB_HOST', $config['host']);
        define('DB_NAME', $config['database']);
        define('DB_USER', $config['username']);
        define('DB_PASS', $config['password']);
    }

    /**
     * It tests the initLaravel method
     *
     * @test
     */
    public function it_should_init_laravel_configurations()
    {
        $object = new System();
        $object->initLaravel();

        // Assert that the configurations were set successfully
        $this->assertEquals(DB_HOST, config('database.connections.workflow.host'));
        $this->assertEquals(DB_NAME, config('database.connections.workflow.database'));
        $this->assertEquals(DB_USER, config('database.connections.workflow.username'));
        $this->assertEquals(DB_PASS, config('database.connections.workflow.password'));
    }
}