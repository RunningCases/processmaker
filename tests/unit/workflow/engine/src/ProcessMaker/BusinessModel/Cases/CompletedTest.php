<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\BusinessModel\Cases\Completed;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Completed
 */
class CompletedTest extends TestCase
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
     * Create complete cases factories
     *
     * @return array
     */
    public function createCompleted()
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
     * This checks the counters is working properly in completed
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Completed::getCounter()
     * @test
     */
    public function it_should_count_cases_completed()
    {
        // Create factories related to the completed cases
        $cases = $this->createCompleted();
        // Create new Completed object
        $completed = new Completed();
        $completed->setUserId($cases['USR_ID']);
        $completed->setUserUid($cases['USR_UID']);
        $result = $completed->getCounter();
        $this->assertTrue($result > 0);
    }
}