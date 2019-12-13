<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Core;

use ProcessMaker\Core\System;
use ProcessMaker\Model\EmailServerModel;
use Tests\TestCase;

class SystemTest extends TestCase
{

    /**
     * Define the required variables
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * It tests the initLaravel method.
     * @test
     * @covers \ProcessMaker\Core\System::initLaravel()
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

    /**
     * This gets the settings for sending email.
     * @test
     * @covers \ProcessMaker\Core\System::getEmailConfiguration()
     */
    public function it_should_get_email_configuration()
    {
        $system = new System();

        //default values
        EmailServerModel::truncate();
        $actual = $system->getEmailConfiguration();
        $this->assertEmpty($actual);

        //new instance
        $emailServer = factory(EmailServerModel::class)->create([
            'MESS_DEFAULT' => 1
        ]);
        $actual = $system->getEmailConfiguration();
        $this->assertNotEmpty($actual);
        $this->assertArrayHasKey('MESS_ENGINE', $actual);
        $this->assertArrayHasKey('OAUTH_CLIENT_ID', $actual);
        $this->assertArrayHasKey('OAUTH_CLIENT_SECRET', $actual);
        $this->assertArrayHasKey('OAUTH_REFRESH_TOKEN', $actual);
    }
}
