<?php

namespace Tests\unit\workflow\engine\classes;

use Tests\TestCase;
use WorkspaceTools;

class WorkflowToolsTest extends TestCase
{
    private $workspaceTools;

    /**
     * Method set up.
     */
    public function setUp()
    {
        parent::setUp();
        $this->workspaceTools = new WorkspaceTools('workflow');
    }

    /**
     * Method tear down.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * This test the addAsyncOptionToSchedulerCommands method.
     * @test
     * @covers \WorkspaceTools::addAsyncOptionToSchedulerCommands()
     */
    public function it_should_test_addAsyncOptionToSchedulerCommands_method()
    {
        //to do: a valid workspace is required.
        if (!defined('HASH_INSTALLATION') || !defined('SYSTEM_HASH')) {
            $message = "A workspace installation is required where the values HASH_INSTALLATION,"
                . "SYSTEM_HASH, db.php exist and are correct.";
            $this->markTestIncomplete($message);
        }
        ob_start();
        $this->workspaceTools->addAsyncOptionToSchedulerCommands(false);
        $string = ob_get_clean();
        $this->assertRegExp("/This was previously updated/", $string);

        ob_start();
        $this->workspaceTools->addAsyncOptionToSchedulerCommands(true);
        $string = ob_get_clean();
        $this->assertRegExp("/Adding \+async option/", $string);
    }
}
