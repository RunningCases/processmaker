<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\BusinessModel\Cases\CasesList;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\CasesList
 */
class CasesListTest extends TestCase
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
     * Create cases factories
     *
     * @return array
     */
    public function createCases()
    {
        $application = factory(Application::class)->states('completed')->create();
        $delegation = factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'CLOSED',
            'DEL_INDEX' => 1,
            'USR_UID' => $application->APP_INIT_USER,
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
        ]);

        return $delegation;
    }

    /**
     * This test construct
     *
     * @covers \ProcessMaker\BusinessModel\Cases\CasesList::__construct()
     * @test
     */
    public function it_test_construct()
    {
        $casesList = new CasesList();
        $this->assertInstanceOf(CasesList::class, $casesList);
    }

    /**
     * This test getAllCounters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\CasesList::getAllCounters()
     * @test
     */
    public function it_return_all_counters()
    {
        $delegation = factory(Delegation::class)->states('foreign_keys')->create();
        $count = new CasesList();
        $result = $count->getAllCounters($delegation->USR_UID);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('CASES_INBOX', $result);
        $this->assertArrayHasKey('CASES_DRAFT', $result);
    }

    /**
     * This test getAllCounters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\CasesList::atLeastOne()
     * @covers \ProcessMaker\BusinessModel\Cases\BatchRouting::atLeastOne()
     * @covers \ProcessMaker\BusinessModel\Cases\Canceled::atLeastOne()
     * @covers \ProcessMaker\BusinessModel\Cases\Completed::atLeastOne()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::atLeastOne()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::atLeastOne()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::atLeastOne()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::atLeastOne()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::atLeastOne()
     * @test
     */
    public function it_return_at_least_one()
    {
        $delegation = factory(Delegation::class)->states('foreign_keys')->create();
        $count = new CasesList();
        $result = $count->atLeastOne($delegation->USR_UID);
        $this->assertNotEmpty($result);
        $firstItem = head($result);
        $this->assertArrayHasKey('item', $firstItem);
        $this->assertArrayHasKey('highlight', $firstItem);
    }
}