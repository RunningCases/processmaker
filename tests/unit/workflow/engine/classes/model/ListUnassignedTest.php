<?php

namespace Tests\unit\workflow\engine\classes\model;

use Criteria;
use Faker\Factory;
use ListUnassigned;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

class ListUnassignedTest extends TestCase
{
    private $listUnassigned;

    /**
     * Set up method.
     */
    public function setUp()
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->listUnassigned = new ListUnassigned();
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
     * @covers ListUnassigned::loadFilters()
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
            'newestthan' => $delegation->DEL_DELEGATE_DATE->format('Y-m-d H:i:s'),
            'oldestthan' => $delegation->DEL_DELEGATE_DATE->format('Y-m-d H:i:s'),
            'appUidCheck' => $delegation->application->APP_UID
        ];
        $this->listUnassigned->loadFilters($criteria, $filters);
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
