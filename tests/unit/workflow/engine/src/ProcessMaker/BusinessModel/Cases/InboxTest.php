<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\BusinessModel\Cases\Inbox;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class InboxTest
 *
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Inbox
 */
class InboxTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Create inbox cases factories
     *
     * @param string
     *
     * @return array
     */
    public function createInbox()
    {
        $delegation = factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
        ]);

        return $delegation;
    }

    /**
     * It tests the getData method without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_should_test_get_data_method_without_filters()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create new Inbox object
        $inbox = new Inbox();
        // Set the user ID
        $inbox->setUserId($cases->USR_ID);
        // Set OrderBYColumn value
        $inbox->setOrderByColumn('APP_NUMBER');
        // Call to getData method
        $res = $inbox->getData();
        // This assert that the expected numbers of results are returned
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with Process Filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_should_test_get_data_by_process_filter()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create new Inbox object
        $inbox = new Inbox();
        $inbox->setUserId($cases->USR_ID);
        $inbox->setProcessId($cases->PRO_ID);
        $inbox->setOrderByColumn('APP_NUMBER');
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method using OrderBy
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_should_test_get_data_by_case_number()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create new Inbox object
        $inbox = new Inbox();
        $inbox->setUserId($cases->USR_ID);
        $inbox->setCaseNumber($cases->APP_NUMBER);
        $inbox->setOrderByColumn('APP_NUMBER');
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method using OrderBy
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_should_test_get_data_by_task_filter()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create new Inbox object
        $inbox = new Inbox();
        $inbox->setUserId($cases->USR_ID);
        $inbox->setTaskId($cases->TAS_ID);
        $res = $inbox->getData();
        $this->assertNotEmpty($res);

    }

    /**
     * It tests the getData method using OrderBy
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_should_test_get_data_by_case_title()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create new Inbox object
        $inbox = new Inbox();
        $inbox->setUserId($cases->USR_ID);
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getCounter method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getCounter()
     * @test
     */
    public function it_should_test_the_counter_for_inbox()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create the Inbox object
        $inbox = new Inbox();
        $inbox->setUserId($cases->USR_ID);
        $res = $inbox->getCounter();
        $this->assertTrue($res > 0);
    }
}