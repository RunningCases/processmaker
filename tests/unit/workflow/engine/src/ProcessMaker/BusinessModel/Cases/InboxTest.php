<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use DateInterval;
use Datetime;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Inbox;
use ProcessMaker\Model\AdditionalTables;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\CaseList;
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
    public function setUp(): void
    {
        parent::setUp();
        Delegation::truncate();
    }

    /**
     * Method tearDown
     */
    public function tearDown(): void
    {
        parent::tearDown();
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
            'DEL_PREVIOUS' => 1,
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
            $delegation = factory(Delegation::class)->states('foreign_keys')->create([
                'DEL_THREAD_STATUS' => 'OPEN',
                'DEL_INDEX' => 2,
                'USR_UID' => $user->USR_UID,
                'USR_ID' => $user->USR_ID,
            ]);
        }
        return $delegation;
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
     * @covers \ProcessMaker\Model\Delegation::scopeInbox()
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
     * It tests the getData method with categoryId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::setCategoryId()
     * @test
     */
    public function it_filter_by_category()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create new Inbox object
        $inbox = new Inbox();
        // Apply filters
        $inbox->setUserId($cases->USR_ID);
        $inbox->setCategoryId(2000);
        // Call to getData method
        $res = $inbox->getData();
        $this->assertEmpty($res);
    }

    /**
     * It tests the getData method with processId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::setProcessId()
     * @test
     */
    public function it_filter_by_process()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create new Inbox object
        $inbox = new Inbox();
        // Apply filters
        $inbox->setUserId($cases->USR_ID);
        $inbox->setProcessId($cases->PRO_ID);
        $inbox->setOrderByColumn('APP_NUMBER');
        // Call to getData method
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with case number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::setCaseNumber()
     * @test
     */
    public function it_filter_by_app_number()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create new Inbox object
        $inbox = new Inbox();
        // Apply filters
        $inbox->setUserId($cases->USR_ID);
        $inbox->setCaseNumber($cases->APP_NUMBER);
        $inbox->setOrderByColumn('APP_NUMBER');
        // Call to getData method
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with case number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::setCasesNumbers()
     * @test
     */
    public function it_filter_by_specific_cases()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create new Inbox object
        $inbox = new Inbox();
        // Apply filters
        $inbox->setUserId($cases->USR_ID);
        $inbox->setCasesNumbers([$cases->APP_NUMBER]);
        $inbox->setOrderByColumn('APP_NUMBER');
        // Call to getData method
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with case number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::setCasesNumbers()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::setRangeCasesFromTo()
     * @test
     */
    public function it_filter_by_range_cases()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create new Inbox object
        $inbox = new Inbox();
        // Apply filters
        $inbox->setUserId($cases->USR_ID);
        $rangeOfCases = $cases->APP_NUMBER . "-" . $cases->APP_NUMBER;
        $inbox->setCasesNumbers([$cases->APP_NUMBER]);
        $inbox->setRangeCasesFromTo([$rangeOfCases]);
        $inbox->setOrderByColumn('APP_NUMBER');
        // Call to getData method
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with taskId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @covers \ProcessMaker\Model\Delegation::scopeTask()
     * @test
     */
    public function it_filter_by_task()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create new Inbox object
        $inbox = new Inbox();
        // Apply filters
        $inbox->setUserId($cases->USR_ID);
        $inbox->setTaskId($cases->TAS_ID);
        // Call to getData method
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with setDelegateFrom and setDelegateTo filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::setDelegateFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::setDelegateTo()
     * @test
     */
    public function it_filter_by_delegate_from_to()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create new Inbox object
        $inbox = new Inbox();
        // Apply filters
        $inbox->setUserId($cases->USR_ID);
        $inbox->setDelegateFrom($cases->DEL_DELEGATE_DATE->format("Y-m-d"));
        $inbox->setDelegateTo($cases->DEL_DELEGATE_DATE->format("Y-m-d"));
        // Call to getData method
        $res = $inbox->getData();
        $this->assertEmpty($res);
    }

    /**
     * It tests the getData method with case title filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::setCaseTitle()
     * @test
     */
    public function it_filter_by_thread_title()
    {

        // Create factories related to the to_do cases
        $delegation = factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
            'DEL_TITLE' => 'Test',
        ]);
        $usrId = $delegation->USR_ID;
        $title = 'Test';
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
     * It tests the getData method with send by filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::setSendBy()
     * @test
     */
    public function it_filter_send_by()
    {
        // Create factories related to the to_do cases
        $cases = $this->createInbox();
        // Create the previous thread with the same user
        $delegation = factory(Delegation::class)->states('foreign_keys')->create([
            'APP_NUMBER' => $cases->APP_NUMBER,
            'APP_UID' => $cases->APP_UID,
            'USR_ID' => $cases->USR_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'DEL_INDEX' => 1,
        ]);
        // Create new Inbox object
        $inbox = new Inbox();
        // Apply filters
        $inbox->setUserId($cases->USR_ID);
        $inbox->setSendBy($cases->USR_ID);
        // Call to getData method
        $res = $inbox->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method using order by column
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::setOrderByColumn()
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

    /**
     * It tests the getCountersByProcesses() method without filters
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_no_filter()
    {
        $user = factory(User::class)->create();
        $process = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID
        ]);
        $inbox = new Inbox();
        $inbox->setUserId($user->USR_ID);
        $inbox->setUserUid($user->USR_UID);
        $res = $inbox->getCountersByProcesses();
        $this->assertCount(2, $res);
    }

    /**
     * It tests the getCountersByProcesses() method with the category filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_category()
    {
        $user = factory(User::class)->create();
        $process = factory(Process::class)->create([
            'CATEGORY_ID' => 1
        ]);
        $process2 = factory(Process::class)->create([
            'CATEGORY_ID' => 2
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID
        ]);
        $inbox = new Inbox();
        $inbox->setUserId($user->USR_ID);
        $inbox->setUserUid($user->USR_UID);
        $res = $inbox->getCountersByProcesses(2);
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCountersByProcesses() method with the top ten filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_top_ten()
    {
        $user = factory(User::class)->create();
        $process1 = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();
        $process3 = factory(Process::class)->create();
        $process4 = factory(Process::class)->create();
        $process5 = factory(Process::class)->create();
        $process6 = factory(Process::class)->create();
        $process7 = factory(Process::class)->create();
        $process8 = factory(Process::class)->create();
        $process9 = factory(Process::class)->create();
        $process10 = factory(Process::class)->create();
        $process11 = factory(Process::class)->create();
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process3->PRO_ID,
            'PRO_UID' => $process3->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process4->PRO_ID,
            'PRO_UID' => $process4->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process5->PRO_ID,
            'PRO_UID' => $process5->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process6->PRO_ID,
            'PRO_UID' => $process6->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process7->PRO_ID,
            'PRO_UID' => $process7->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process8->PRO_ID,
            'PRO_UID' => $process8->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process9->PRO_ID,
            'PRO_UID' => $process9->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process10->PRO_ID,
            'PRO_UID' => $process10->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process11->PRO_ID,
            'PRO_UID' => $process11->PRO_UID
        ]);
        $inbox = new Inbox();
        $inbox->setUserId($user->USR_ID);
        $inbox->setUserUid($user->USR_UID);
        $res = $inbox->getCountersByProcesses(null, true);
        $this->assertCount(10, $res);
    }

    /**
     * It tests the getCountersByProcesses() method with the processes filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_processes()
    {
        $user = factory(User::class)->create();
        $process = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID
        ]);
        $inbox = new Inbox();
        $inbox->setUserId($user->USR_ID);
        $inbox->setUserUid($user->USR_UID);
        $res = $inbox->getCountersByProcesses(null, false, [$process->PRO_ID]);
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCountersByRange() method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getCountersByRange()
     * @test
     */
    public function it_should_test_get_counters_by_range_method()
    {
        $user = factory(User::class)->create();
        $process = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_DELEGATE_DATE' => '2021-05-20 09:52:32'
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'DEL_DELEGATE_DATE' => '2021-05-25 09:52:32'
        ]);
        $inbox = new Inbox();
        $inbox->setUserId($user->USR_ID);
        $inbox->setUserUid($user->USR_UID);
        $res = $inbox->getCountersByRange();
        $this->assertCount(2, $res);

        $res = $inbox->getCountersByRange(null, null, null, 'month');
        $this->assertCount(1, $res);

        $res = $inbox->getCountersByRange(null, null, null, 'year');
        $this->assertCount(1, $res);

        $res = $inbox->getCountersByRange($process2->PRO_ID);
        $this->assertCount(1, $res);

        $res = $inbox->getCountersByRange(null, '2021-05-20', '2021-05-23');
        $this->assertCount(1, $res);
    }

    /**
     * This tests the getCustomListCount() method.
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getCustomListCount()
     * @test
     */
    public function it_should_test_getCustomListCounts_method()
    {
        $this->markTestIncomplete('Illegal mix of collations');
        $cases = $this->createMultipleInbox(3);

        $additionalTables = factory(AdditionalTables::class)->create([
            'PRO_UID' => $cases->PRO_UID
        ]);
        $query = ""
            . "CREATE TABLE IF NOT EXISTS `{$additionalTables->ADD_TAB_NAME}` ("
            . "`APP_UID` varchar(32) NOT NULL,"
            . "`APP_NUMBER` int(11) NOT NULL,"
            . "`APP_STATUS` varchar(10) NOT NULL,"
            . "`VAR1` varchar(255) DEFAULT NULL,"
            . "`VAR2` varchar(255) DEFAULT NULL,"
            . "`VAR3` varchar(255) DEFAULT NULL,"
            . "PRIMARY KEY (`APP_UID`),"
            . "KEY `indexTable` (`APP_UID`))";
        DB::statement($query);

        $caseList = factory(CaseList::class)->create([
            'CAL_TYPE' => 'inbox',
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'USR_ID' => $cases->USR_ID
        ]);

        $inbox = new Inbox();
        $inbox->setUserId($cases->USR_ID);
        $inbox->setUserUid($cases->USR_UID);

        $res = $inbox->getCustomListCount($caseList->CAL_ID, 'inbox');

        //assertions
        $this->assertArrayHasKey('label', $res);
        $this->assertArrayHasKey('name', $res);
        $this->assertArrayHasKey('description', $res);
        $this->assertArrayHasKey('tableName', $res);
        $this->assertArrayHasKey('total', $res);

        $this->assertEquals($additionalTables->ADD_TAB_NAME, $res['tableName']);
        $this->assertEquals(1, $res['total']);
    }

    /**
     * This tests the getCasesRisk() method with on time filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getCasesRisk()
     * @test
     */
    public function it_tests_get_cases_risk_on_time()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff1Day = new DateInterval('P1D');
        $diff2Days = new DateInterval('P2D');
        $user = factory(User::class)->create();
        $process = factory(Process::class)->create();
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $date->add($diff1Day),
            'DEL_TASK_DUE_DATE' => $date->add($diff2Days)
        ]);
        $inbox = new Inbox();
        $inbox->setUserId($user->USR_ID);
        $inbox->setUserUid($user->USR_UID);
        $res = $inbox->getCasesRisk($process->PRO_ID);
        $this->assertCount(1, $res);
    }

    /**
     * This tests the getCasesRisk() method with at risk filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getCasesRisk()
     * @test
     */
    public function it_tests_get_cases_risk_at_risk()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff2Days = new DateInterval('P2D');
        $user = factory(User::class)->create();
        $process = factory(Process::class)->create();
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $currentDate,
            'DEL_TASK_DUE_DATE' => $date->add($diff2Days)
        ]);
        $inbox = new Inbox();
        $inbox->setUserId($user->USR_ID);
        $inbox->setUserUid($user->USR_UID);
        $res = $inbox->getCasesRisk($process->PRO_ID, null, null, "AT_RISK");
        $this->assertCount(1, $res);
    }

    /**
     * This tests the getCasesRisk() method with overdue filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getCasesRisk()
     * @test
     */
    public function it_tests_get_cases_risk_overdue()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff2Days = new DateInterval('P2D');
        $user = factory(User::class)->create();
        $process = factory(Process::class)->create();
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $currentDate,
            'DEL_TASK_DUE_DATE' => $date->sub($diff2Days)
        ]);
        $inbox = new Inbox();
        $inbox->setUserId($user->USR_ID);
        $inbox->setUserUid($user->USR_UID);
        $res = $inbox->getCasesRisk($process->PRO_ID, null, null, "OVERDUE");
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCounterMetrics method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getCounterMetrics()
     * @test
     */
    public function it_tests_get_counter_metrics()
    {
        $this->createInbox();
        $inbox = new Inbox();
        $res = $inbox->getCounterMetrics();
        $this->assertTrue($res > 0);
    }
}
