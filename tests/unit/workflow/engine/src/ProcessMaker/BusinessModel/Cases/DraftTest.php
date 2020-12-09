<?php

namespace Tests\unit\workflow\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\BusinessModel\Cases\Draft;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Draft
 */
class DraftTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Method set up.
     */
    public function setUp()
    {
        parent::setUp();
        $this->markTestIncomplete();
    }

    /**
     * Create draft cases factories
     *
     * @param string
     *
     * @return array
     */
    public function createDraft()
    {
        $application = factory(Application::class)->states('draft')->create();
        $delegation = factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
        ]);

        return $delegation;
    }

    /**
     * This checks the counters is working properly in draft
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getCounter()
     * @test
     */
    public function it_should_count_cases()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases->USR_ID);
        $result = $draft->getCounter();
        $this->assertTrue($result > 0);
    }

    /**
     * It tests the getData method without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_test_get_data_method_without_filters()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $inbox = new Inbox();
        // Set the user ID
        $inbox->setUserId($cases->USR_ID);
        $inbox->setOrderByColumn('APP_NUMBER');
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with Process Filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_test_get_data_by_process_filter()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $inbox = new Draft();
        $inbox->setUserId($cases->USR_ID);
        $inbox->setProcessId($cases->PRO_ID);
        $inbox->setOrderByColumn('APP_NUMBER');
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method using OrderBy
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_test_get_data_by_case_number()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $inbox = new Draft();
        $inbox->setUserId($cases->USR_ID);
        $inbox->setCaseNumber($cases->APP_NUMBER);
        $inbox->setOrderByColumn('APP_NUMBER');
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method using OrderBy
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_test_get_data_by_task_filter()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $inbox = new Draft();
        $inbox->setUserId($cases->USR_ID);
        $inbox->setTaskId($cases->TAS_ID);
        $res = $inbox->getData();
        $this->assertNotEmpty($res);

    }

    /**
     * It tests the getData method using OrderBy
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_test_get_data_by_case_title()
    {
        // Create factories related to the to_do cases
        $cases = $this->createDraft();
        // Create new Draft object
        $inbox = new Draft();
        $inbox->setUserId($cases->USR_ID);
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }
}