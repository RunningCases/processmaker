<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use ProcessMaker\BusinessModel\Cases\BatchRouting;
use ProcessMaker\Model\Consolidated;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * Class BatchRoutingTest
 * 
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\BatchRouting
 */
class BatchRoutingTest extends TestCase
{
    /**
     * This method is called before the first test of this test class is run.
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::truncateNonInitialModels();
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::truncateNonInitialModels();
    }

    /**
     * Method set up.
     */
    public function setUp(): void
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
        $consolidated = Consolidated::factory()->foreign_keys()->create();
        $delegation = Delegation::factory()->foreign_keys()->create([
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
     * @covers \ProcessMaker\BusinessModel\Cases\BatchRouting::filters()
     * @test
     */
    public function it_test_extended_methods()
    {
        // Create new BatchRouting object
        $consolidated = new BatchRouting();
        $result = $consolidated->getColumnsView();
        $this->assertNotEmpty($result);
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