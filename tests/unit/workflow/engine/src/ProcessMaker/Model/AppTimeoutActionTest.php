<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\AppTimeoutAction;
use Tests\TestCase;

/**
 * Class DelegationTest
 *
 * @coversDefaultClass \ProcessMaker\Model\AppTimeoutAction
 */
class AppTimeoutActionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test set and get the caseUid property
     *
     * @covers \ProcessMaker\Model\AppTimeoutAction::setCaseUid()
     * @covers \ProcessMaker\Model\AppTimeoutAction::getCaseUid()
     * @test
     */
    public function it_set_get_case_uid()
    {
        factory(AppTimeoutAction::class)->create();
        $timeout = factory(AppTimeoutAction::class)->create();
        $timeout->setCaseUid($timeout->APP_UID);
        $this->assertEquals($timeout->getCaseUid(), $timeout->APP_UID);
    }

    /**
     * Test set and get the index property
     *
     * @covers \ProcessMaker\Model\AppTimeoutAction::setIndex()
     * @covers \ProcessMaker\Model\AppTimeoutAction::getIndex()
     * @test
     */
    public function it_set_get_index()
    {
        factory(AppTimeoutAction::class)->create();
        $timeout = factory(AppTimeoutAction::class)->create();
        $timeout->setIndex($timeout->DEL_INDEX);
        $this->assertEquals($timeout->getIndex(), $timeout->DEL_INDEX);
    }

    /**
     * Test a query to only include a specific case
     *
     * @covers \ProcessMaker\Model\AppTimeoutAction::scopeCase()
     * @test
     */
    public function it_filter_a_specific_case()
    {
        factory(AppTimeoutAction::class)->create();
        $timeout = factory(AppTimeoutAction::class)->create();
        $this->assertCount(1, $timeout->case($timeout->APP_UID)->get());
    }

    /**
     * Test scope a query to only include a specific case
     *
     * @covers \ProcessMaker\Model\AppTimeoutAction::scopeIndex()
     * @test
     */
    public function it_filter_a_specific_index()
    {
        factory(AppTimeoutAction::class)->create();
        $timeout = factory(AppTimeoutAction::class)->create();
        $this->assertCount(1, $timeout->case($timeout->APP_UID)->index($timeout->DEL_INDEX)->get());
    }

    /**
     * This checks it returns information about the self service timeout in a sequential thread
     *
     * @covers \ProcessMaker\Model\AppTimeoutAction::cases()
     * @test
     */
    public function it_return_the_case_executed_once_one_thread()
    {
        $records = factory(AppTimeoutAction::class, 5)->create();
        foreach ($records as $row) {
            $appUid = $row->APP_UID;
            $delIndex = $row->DEL_INDEX;
        }

        $appTimeout = new AppTimeoutAction();
        $appTimeout->setCaseUid($appUid);
        $appTimeout->setIndex($delIndex);
        $caseExecuted = $appTimeout->cases();
        $this->assertNotEmpty($caseExecuted);
    }

    /**
     * This checks it returns information about the self service timeout in a parallel thread
     *
     * @covers \ProcessMaker\Model\AppTimeoutAction::cases()
     * @test
     */
    public function it_return_the_case_executed_once_more_than_one_thread()
    {
        $records = factory(AppTimeoutAction::class, 5)->create();
        foreach ($records as $row) {
            $appUid = $row->APP_UID;
            $delIndex = $row->DEL_INDEX;
        }
        // Create other thread in the same case
        factory(AppTimeoutAction::class)->create([
            'APP_UID' => $appUid,
            'DEL_INDEX' => $delIndex + 1,
        ]);

        $appTimeout = new AppTimeoutAction();
        $appTimeout->setCaseUid($appUid);
        $appTimeout->setIndex($delIndex);
        $caseExecuted = $appTimeout->cases();
        $this->assertNotEmpty($caseExecuted);
    }
}
