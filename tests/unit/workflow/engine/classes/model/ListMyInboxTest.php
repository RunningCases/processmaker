<?php

namespace Tests\unit\workflow\engine\classes\model;

use Criteria;
use Faker\Factory;
use ListMyInbox;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

class ListMyInboxTest extends TestCase
{
    private $listMyInbox;

    /**
     * Set up method.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->listMyInbox = new ListMyInbox();
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
     * @covers ListMyInbox::loadFilters()
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
            'process' => $delegation->process->PRO_UID,
            'category' => $this->faker->word,
            'dateFrom' => '',
            'dateTo' => ''
        ];
        $this->listMyInbox->loadFilters($criteria, $filters);
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
