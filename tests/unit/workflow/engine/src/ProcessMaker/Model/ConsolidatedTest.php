<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Consolidated;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * @coversDefaultClass \ProcessMaker\Model\Consolidated
 */
class ConsolidatedTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Method set up.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Create consolidated cases factories
     *
     * @return array
     */
    public function createConsolidated()
    {
        $consolidated = factory(Consolidated::class)->states('foreign_keys')->create();
        $delegation = factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'TAS_UID' => $consolidated->TAS_UID,
        ]);

        return $delegation;
    }

    /**
     * This checks the counters is working properly in draft
     *
     * @covers \ProcessMaker\Model\Consolidated::getCounterActive()
     * @test
     */
    public function it_should_count_cases_consolidated()
    {
        // Create factories related to the consolidated
        $cases = $this->createConsolidated();
        // Create new Consolidated object
        $consolidated = new Consolidated();
        $result = $consolidated->getCounterActive();
        $this->assertTrue($result > 0);
    }

    /**
     * This checks the counters is working properly in consolidated
     *
     * @covers \ProcessMaker\Model\Consolidated::getCounterActive()
     * @test
     */
    public function it_should_count_cases()
    {
        // Create factories related to the consolidated
        $cases = $this->createConsolidated();
        // Create new Consolidated object
        $consolidated = new Consolidated();
        $result = $consolidated->getConsolidated();
        $this->assertTrue($result > 0);
    }

    /**
     * This tests the scopeJoinProcess
     *
     * @covers \ProcessMaker\Model\Consolidated::scopeJoinProcess()
     * @test
     */
    public function it_should_test_scope_join_process()
    {
        $query = factory(Consolidated::class)->states('foreign_keys')->create();
        $consolidated = new Consolidated();
        $this->assertCount(1, $consolidated->scopeJoinProcess($query)->get());
    }

    /**
     * This tests the scopeJoinTask
     *
     * @covers \ProcessMaker\Model\Consolidated::scopeJoinTask()
     * @test
     */
    public function it_should_test_scope_join_task()
    {
        $query = factory(Consolidated::class)->states('foreign_keys')->create();
        $consolidated = new Consolidated();
        $this->assertCount(1, $consolidated->scopeJoinTask($query)->get());
    }
}
