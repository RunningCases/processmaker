<?php

namespace Tests\unit\workflow\engine\classes\model;

use Criteria;
use Faker\Factory;
use ListPaused;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

class ListPausedTest extends TestCase
{
    private $listPaused;

    /**
     * Set up method.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->listPaused = new ListPaused();
    }

    /**
     * Tear down method,
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     * @covers ListPaused::loadFilters()
     */
    public function it_should_test_loadFilters_method()
    {
        $delegation = factory(Delegation::class)->create([
            'DEL_THREAD_STATUS' => 'CLOSED',
        ]);

        $criteria = new Criteria('workflow');
        $filters = [
            'filter' => '',
            'search' => $delegation->application->APP_UID,
            'caseLink' => $delegation->application->APP_UID,
            'process' => $delegation->process->PRO_UID,
            'category' => $delegation->process->PRO_CATEGORY,
            'filterStatus' => 'ON_TIME' //ON_TIME,AT_RISK,OVERDUE
        ];
        $this->listPaused->loadFilters($criteria, $filters);
        $joinsMC = $criteria->getJoinsMC();

        $this->assertNotEmpty($joinsMC);
        $this->assertObjectHasAttribute('conditions', $joinsMC[0]);

        $expected = [
            'PROCESS.PRO_CATEGORY',
            "'{$filters['category']}'"
        ];
        $this->assertContains($expected, $joinsMC[0]->conditions);
    }
}
