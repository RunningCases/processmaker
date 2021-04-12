<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Inbox;
use ProcessMaker\Model\Application;
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
     * Method set up.
     */
    public function setUp()
    {
        parent::setUp();
    }

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
     * Create many inbox cases for one user
     * 
     * @param int
     * @return object
     */
    public function createMultipleInbox($cases)
    {
        $user = factory(\ProcessMaker\Model\User::class)->create();
        
        for ($i = 0; $i < $cases; $i = $i + 1) {
            factory(Delegation::class)->states('foreign_keys')->create([
                'DEL_THREAD_STATUS' => 'OPEN',
                'DEL_INDEX' => 2,
                'USR_UID' => $user->USR_UID,
                'USR_ID' => $user->USR_ID,
            ]);
        }
        return $user;
    }

    /**
     * It tests the getCounter method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getCounter()
     * @test
     */
    public function it_get_counter()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create the Inbox object
        $inbox = new Inbox();
        $inbox->setUserId($cases->USR_ID);
        $res = $inbox->getCounter();
        $this->assertTrue($res > 0);
    }

    /**
     * It tests the getData method without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @test
     */
    public function it_get_result_without_filters()
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
     * It tests the getData method with processId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @test
     */
    public function it_filter_by_process()
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
     * It tests the getData method with case number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @test
     */
    public function it_filter_by_app_number()
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
     * It tests the getData method with taskId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @test
     */
    public function it_filter_by_task()
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
     * It tests the getData method with case title filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @test
     */
    public function it_filter_by_thread_title()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        $usrId = $cases->USR_ID;
        $title = $cases->DEL_TITLE;
        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();
        // Create new Inbox object
        $inbox = new Inbox();
        $inbox->setUserId($usrId);
        // Set the title
        $inbox->setCaseTitle($title);
        // Get the data
        $result = $inbox->getData();
        // Asserts
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getData method using order by column
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @test
     */
    public function it_order_by_column()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        $columnsView = [
            'APP_NUMBER',
            'DEL_TITLE',
            'PRO_TITLE',
            'TAS_TITLE',
            'DEL_TASK_DUE_DATE',
            'DEL_DELEGATE_DATE'
        ];
        $index = array_rand($columnsView);
        // Create new Inbox object
        $inbox = new Inbox();
        $inbox->setUserId($cases->USR_ID);
        // Define the column to order
        $inbox->setOrderByColumn($columnsView[$index]);
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getPagingCounters() method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getPagingCounters()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @test
     */
    public function it_should_test_get_paging_counters_method()
    {
        $cases = $this->createMultipleInbox(3);
        $inbox = new Inbox();
        $inbox->setUserId($cases->USR_ID);
        $inbox->setUserUid($cases->USR_UID);

        $res = $inbox->getPagingCounters();
        $this->assertEquals(3, $res);

        $delegation = Delegation::select()->where('USR_ID', $cases->USR_ID)->first();

        $inbox->setCaseNumber($delegation->APP_NUMBER);
        $inbox->setProcessId($delegation->PRO_ID);
        $inbox->setTaskId($delegation->TAS_ID);
        $inbox->setCaseUid($delegation->APP_UID);

        $res = $inbox->getPagingCounters();
        $this->assertEquals(1, $res);
    }
}