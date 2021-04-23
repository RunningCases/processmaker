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
}