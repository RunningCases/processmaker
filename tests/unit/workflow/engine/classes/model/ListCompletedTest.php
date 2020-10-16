<?php

namespace Tests\unit\workflow\engine\classes\model;

use Criteria;
use Faker\Factory;
use ListCompleted;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

class ListCompletedTest extends TestCase
{
    private $listCompleted;

    /**
     * Set up method.
     */
    public function setUp()
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->listCompleted = new ListCompleted();
    }

    /**
     * Tear down method,
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     * @covers ListCompleted::loadFilters()
     */
    public function it_should_test_loadFilters_method()
    {
        $delegation = factory(Delegation::class)->create([
            'DEL_THREAD_STATUS' => 'CLOSED',
        ]);

        $criteria = new Criteria('workflow');

        //the ListCompleted contains fields that were removed (DEL_DELEGATE_DATE,DEL_INIT_DATE) but are still used,
        //these places are not reachable in code coverage.
        $filters = [
            'filter' => '', //read,unread
            'search' => $delegation->application->APP_UID,
            'process' => $delegation->process->PRO_UID,
            'category' => $delegation->process->PRO_CATEGORY,
            'dateFrom' => '',
            'dateTo' => ''
        ];
        $this->listCompleted->loadFilters($criteria, $filters);
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
