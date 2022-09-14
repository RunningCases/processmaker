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
    public function setUp(): void
    {
        parent::setUp();
        $this->workspaceTools = new WorkspaceTools('workflow');
    }

    /**
     * Method tear down.
     */
    public function tearDown(): void
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
        //method "WorkspaceTools::initPropel(true)" crashes all connections
        $this->markTestSkipped("WorkspaceTools::initPropel(true) crashes all connections");

        ob_start();
        $this->workspaceTools->addAsyncOptionToSchedulerCommands(false);
        $string = ob_get_clean();
        $this->assertMatchesRegularExpression("/This was previously updated/", $string);

        ob_start();
        $this->workspaceTools->addAsyncOptionToSchedulerCommands(true);
        $string = ob_get_clean();
        $this->assertMatchesRegularExpression("/Adding \+async option/", $string);
    }
}
