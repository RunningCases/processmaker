<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\BusinessModel\Cases\BatchRouting;
use ProcessMaker\Model\Consolidated;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\BatchRouting
 */
class BatchRoutingTest extends TestCase
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
     * This test the extended function, currently are not implemented
     *
     * @covers \ProcessMaker\BusinessModel\Cases\BatchRouting::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\BatchRouting::getData()
     * @test
     */
    public function it_test_extended_methods()
    {
        // Create new BatchRouting object
        $consolidated = new BatchRouting();
        $result = $consolidated->getColumnsView();
        $this->assertEmpty($result);
        $result = $consolidated->getData();
        $this->assertEmpty($result);
    }

    /**
     * This checks the counters is working properly in batch routing
     *
     * @covers \ProcessMaker\BusinessModel\Cases\BatchRouting::getCounter()
     * @test
     */
    public function it_should_count_cases_consolidated()
    {
        // Create factories related to the consolidated cases
        $cases = $this->createConsolidated();
        // Create new batch routing object
        $consolidated = new BatchRouting();
        $consolidated->setUserId($cases['USR_ID']);
        $consolidated->setUserUid($cases['USR_UID']);
        $result = $consolidated->getCounter();
        $this->assertTrue($result > 0);
    }
}