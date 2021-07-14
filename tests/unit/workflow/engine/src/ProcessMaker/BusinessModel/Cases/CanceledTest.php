<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\BusinessModel\Cases\Canceled;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Canceled
 */
class CanceledTest extends TestCase
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
     * Create inbox cases factories
     *
     * @return array
     */
    public function createCanceled()
    {
        $application = factory(Application::class)->states('canceled')->create();
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
     * This test the extended function, currently are not implemented
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Canceled::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Canceled::getData()
     * @test
     */
    public function it_test_extended_methods()
    {
        // Create new batch Canceled object
        $consolidated = new Canceled();
        $result = $consolidated->getColumnsView();
        $this->assertEmpty($result);
        $result = $consolidated->getData();
        $this->assertEmpty($result);
    }

    /**
     * This checks the counters is working properly in canceled
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Canceled::getCounter()
     * @test
     */
    public function it_should_count_cases_completed()
    {
        // Create factories related to the canceled cases
        $cases = $this->createCanceled();
        // Create new Canceled object
        $canceled = new Canceled();
        $canceled->setUserId($cases['USR_ID']);
        $canceled->setUserUid($cases['USR_UID']);
        $result = $canceled->getCounter();
        $this->assertTrue($result > 0);
    }
}