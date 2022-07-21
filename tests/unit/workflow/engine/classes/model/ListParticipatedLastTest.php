<?php

namespace Tests\unit\workflow\engine\classes\model;

use Criteria;
use Faker\Factory;
use ListParticipatedLast;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

class ListParticipatedLastTest extends TestCase
{
    private $listParticipatedLast;

    /**
     * Set up method.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->listParticipatedLast = new ListParticipatedLast();
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
     * @covers ListParticipatedLast::loadFilters()
     */
    public function it_should_test_loadFilters_method()
    {
        $delegation = Delegation::factory()->create([
            'DEL_THREAD_STATUS' => 'CLOSED',
        ]);

        $criteria = new Criteria('workflow');
        $filters = [
            'filter' => '',
            'search' => $delegation->application->APP_UID,
            'caseLink' => $delegation->application->APP_UID,
            'process' => $delegation->process->PRO_UID,
            'category' => $this->faker->word,
            'dateFrom' => '',
            'dateTo' => '',
            'filterStatus' => 'ON_TIME', //ON_TIME,AT_RISK,OVERDUE
            'newestthan' => $delegation->DEL_DELEGATE_DATE->format('Y-m-d H:i:s'),
            'oldestthan' => $delegation->DEL_DELEGATE_DATE->format('Y-m-d H:i:s')
        ];
        $this->listParticipatedLast->loadFilters($criteria, $filters);
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
