<?php

namespace Tests\unit\workflow\engine\classes\model;

use Criteria;
use Faker\Factory;
use ListInbox;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

class ListInboxTest extends TestCase
{
    private $listInbox;

    /**
     * Set up method.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->listInbox = new ListInbox();
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
     * @covers ListInbox::loadFilters()
     */
    public function it_should_test_loadFilters_method()
    {
        $delegation = factory(Delegation::class)->create([
            'DEL_THREAD_STATUS' => 'CLOSED',
        ]);

        $criteria = new Criteria('workflow');
        $filters = [
            'action' => 'draft', //draft,to_revise,to_reassign
            'usr_uid' => $delegation->user->USR_UID,
            'filter' => '',
            'search' => $delegation->application->APP_UID,
            'caseLink' => $delegation->application->APP_UID,
            'process' => $delegation->process->PRO_UID,
            'category' => $delegation->process->PRO_CATEGORY,
            'dateFrom' => '',
            'dateTo' => '',
            'filterStatus' => 'ON_TIME', //ON_TIME,AT_RISK,OVERDUE
            'newestthan' => $delegation->DEL_DELEGATE_DATE->format('Y-m-d H:i:s'),
            'oldestthan' => $delegation->DEL_DELEGATE_DATE->format('Y-m-d H:i:s'),
            'appUidCheck' => $delegation->application->APP_UID
        ];
        $this->listInbox->loadFilters($criteria, $filters);
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
