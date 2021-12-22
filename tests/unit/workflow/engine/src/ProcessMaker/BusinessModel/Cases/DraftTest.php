<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use DateInterval;
use Datetime;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Draft;
use ProcessMaker\Model\AdditionalTables;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\CaseList;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class DraftTest
 * 
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
        Delegation::truncate();
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
            'USR_UID' => $application->APP_INIT_USER,
            'USR_ID' => $application->APP_INIT_USER_ID,
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
        ]);

        return $delegation;
    }

    /**
     * Create many draft cases for one user
     * 
     * @param int
     * @return object
     */
    public function createManyDraft($cases)
    {
        $user = factory(\ProcessMaker\Model\User::class)->create();

        for ($i = 0; $i < $cases; $i = $i + 1) {
            $application = factory(Application::class)->states('draft')->create([
                'APP_INIT_USER' => $user->USR_UID,
                'APP_INIT_USER_ID' => $user->USR_ID,
                'APP_CUR_USER' => $user->USR_UID,
            ]);
            factory(Delegation::class)->states('foreign_keys')->create([
                'DEL_THREAD_STATUS' => 'OPEN',
                'DEL_INDEX' => 1,
                'APP_UID' => $application->APP_UID,
                'APP_NUMBER' => $application->APP_NUMBER,
                'USR_UID' => $application->APP_INIT_USER,
                'USR_ID' => $application->APP_INIT_USER_ID,
            ]);
        }

        return $user;
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
        $draft->setUserId($cases['USR_ID']);
        $draft->setUserUid($cases['USR_UID']);
        $result = $draft->getCounter();
        $this->assertTrue($result > 0);
    }

    /**
     * It tests the getData method without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @covers \ProcessMaker\Model\Delegation::scopeDraft()
     * @test
     */
    public function it_get_result_without_filters()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        // Set the user ID
        $draft->setUserId($cases['USR_ID']);
        $draft->setOrderByColumn('APP_NUMBER');
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with categoryId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_category()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases['USR_ID']);
        $draft->setCategoryId(2000);
        $res = $draft->getData();
        $this->assertEmpty($res);
    }

    /**
     * It tests the getData method with processId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_process()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases['USR_ID']);
        $draft->setProcessId($cases['PRO_ID']);
        $draft->setOrderByColumn('APP_NUMBER');
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with case number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_app_number()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases['USR_ID']);
        $draft->setCaseNumber($cases['APP_NUMBER']);
        $draft->setOrderByColumn('APP_NUMBER');
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with case number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_specific_cases()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases['USR_ID']);
        $draft->setCasesNumbers([$cases['APP_NUMBER']]);
        $draft->setOrderByColumn('APP_NUMBER');
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with case number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_range_cases()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases['USR_ID']);
        $rangeOfCases = $cases['APP_NUMBER'] . "-" . $cases['APP_NUMBER'];
        $draft->setRangeCasesFromTo([$rangeOfCases]);
        $draft->setOrderByColumn('APP_NUMBER');
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with taskId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_task()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases['USR_ID']);
        $draft->setTaskId($cases['TAS_ID']);
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with case title filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_thread_title()
    {
        // Create factories related to the to_do cases
        $cases = $this->createDraft();
        $usrId = $cases['USR_ID'];
        $title = $cases['DEL_TITLE'];
        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($usrId);
        // Set the title
        $draft->setCaseTitle($title);
        // Get the data
        $res = $draft->getData();
        // Asserts
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method using order by column
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_order_by_column()
    {
        // Create factories related to the to_do cases
        $cases = $this->createDraft();
        $columnsView = [
            'APP_NUMBER',
            'DEL_TITLE',
            'PRO_TITLE',
        ];
        $index = array_rand($columnsView);
        // Create new Inbox object
        $draft = new Draft();
        $draft->setUserId($cases['USR_ID']);
        // Define the column to order
        $draft->setOrderByColumn($columnsView[$index]);
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getPagingCounters() method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getPagingCounters()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_should_test_get_paging_counters_method()
    {
        $cases = $this->createManyDraft(3);

        $draft = new Draft();
        $draft->setUserId($cases->USR_ID);
        $draft->setUserUid($cases->USR_UID);

        $res = $draft->getPagingCounters();
        $this->assertEquals(3, $res);

        $delegation = Delegation::select()->where('USR_ID', $cases->USR_ID)->first();

        $draft->setCaseNumber($delegation->APP_NUMBER);
        $draft->setProcessId($delegation->PRO_ID);
        $draft->setTaskId($delegation->TAS_ID);
        $draft->setCaseUid($delegation->APP_UID);

        $res = $draft->getPagingCounters();

        $this->assertEquals(1, $res);
    }

    /**
     * It tests the getCountersByProcesses() method without filters
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_no_filter()
    {
        $process = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();
        $user = factory(User::class)->create();
        $application1 = factory(Application::class)->states('draft')->create([
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $application2 = factory(Application::class)->states('draft')->create([
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application1->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application2->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application2->APP_UID,
            'APP_NUMBER' => $application2->APP_NUMBER,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID
        ]);
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setUserUid($user->USR_ID);
        $res = $draft->getCountersByProcesses();
        $this->assertCount(2, $res);
    }

    /**
     * It tests the getCountersByProcesses() method with the category filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getCountersByProcesses()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_category()
    {
        $process = factory(Process::class)->create([
            'CATEGORY_ID' => 1
        ]);
        $process2 = factory(Process::class)->create([
            'CATEGORY_ID' => 2
        ]);
        $user = factory(User::class)->create();
        $application = factory(Application::class, 5)->states('draft')->create([
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[0]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[0]->APP_UID,
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[1]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[1]->APP_UID,
            'APP_NUMBER' => $application[1]->APP_NUMBER,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[2]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[2]->APP_UID,
            'APP_NUMBER' => $application[2]->APP_NUMBER,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[3]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[3]->APP_UID,
            'APP_NUMBER' => $application[3]->APP_NUMBER,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[4]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[4]->APP_UID,
            'APP_NUMBER' => $application[4]->APP_NUMBER,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID
        ]);
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setUserUid($user->USR_ID);
        $res = $draft->getCountersByProcesses(2);
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCountersByProcesses() method with the top ten filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getCountersByProcesses()
     * @covers \ProcessMaker\Model\Delegation::scopeTopTen()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_top_ten()
    {
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
        $user = factory(User::class)->create();
        $application = factory(Application::class, 14)->states('draft')->create([
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[0]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[0]->APP_UID,
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[1]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[1]->APP_UID,
            'APP_NUMBER' => $application[1]->APP_NUMBER,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[2]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[2]->APP_UID,
            'APP_NUMBER' => $application[2]->APP_NUMBER,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[3]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[3]->APP_UID,
            'APP_NUMBER' => $application[3]->APP_NUMBER,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[4]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[4]->APP_UID,
            'APP_NUMBER' => $application[4]->APP_NUMBER,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[5]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[5]->APP_UID,
            'APP_NUMBER' => $application[5]->APP_NUMBER,
            'PRO_ID' => $process3->PRO_ID,
            'PRO_UID' => $process3->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[6]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[6]->APP_UID,
            'APP_NUMBER' => $application[6]->APP_NUMBER,
            'PRO_ID' => $process4->PRO_ID,
            'PRO_UID' => $process4->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[7]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[7]->APP_UID,
            'APP_NUMBER' => $application[7]->APP_NUMBER,
            'PRO_ID' => $process5->PRO_ID,
            'PRO_UID' => $process5->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[8]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[8]->APP_UID,
            'APP_NUMBER' => $application[8]->APP_NUMBER,
            'PRO_ID' => $process6->PRO_ID,
            'PRO_UID' => $process6->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[9]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[9]->APP_UID,
            'APP_NUMBER' => $application[9]->APP_NUMBER,
            'PRO_ID' => $process7->PRO_ID,
            'PRO_UID' => $process7->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[10]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[10]->APP_UID,
            'APP_NUMBER' => $application[10]->APP_NUMBER,
            'PRO_ID' => $process8->PRO_ID,
            'PRO_UID' => $process8->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[11]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[11]->APP_UID,
            'APP_NUMBER' => $application[11]->APP_NUMBER,
            'PRO_ID' => $process9->PRO_ID,
            'PRO_UID' => $process9->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[12]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[12]->APP_UID,
            'APP_NUMBER' => $application[12]->APP_NUMBER,
            'PRO_ID' => $process10->PRO_ID,
            'PRO_UID' => $process10->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[13]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[13]->APP_UID,
            'APP_NUMBER' => $application[13]->APP_NUMBER,
            'PRO_ID' => $process11->PRO_ID,
            'PRO_UID' => $process11->PRO_UID
        ]);
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setUserUid($user->USR_ID);
        $res = $draft->getCountersByProcesses(null, true);
        $this->assertCount(10, $res);
    }

    /**
     * It tests the getCountersByProcesses() method with the processes filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_processes()
    {
        $process = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();
        $user = factory(User::class)->create();
        $application = factory(Application::class, 14)->states('draft')->create([
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[0]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[0]->APP_UID,
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[1]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[1]->APP_UID,
            'APP_NUMBER' => $application[1]->APP_NUMBER,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID
        ]);
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setUserUid($user->USR_ID);
        $res = $draft->getCountersByProcesses(null, false, [$process->PRO_ID]);
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCountersByRange() method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getCountersByRange()
     * @covers \ProcessMaker\BusinessModel\Cases\AbstractCases::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_range_method()
    {
        $process1 = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();
        $user = factory(User::class)->create();
        $application = factory(Application::class, 4)->states('draft')->create([
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[0]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[0]->APP_UID,
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_DELEGATE_DATE' => '2021-05-20 09:52:32'
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[1]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[1]->APP_UID,
            'APP_NUMBER' => $application[1]->APP_NUMBER,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_DELEGATE_DATE' => '2021-05-21 09:52:32'
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[2]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[2]->APP_UID,
            'APP_NUMBER' => $application[2]->APP_NUMBER,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_DELEGATE_DATE' => '2021-05-22 00:00:00'
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[3]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[3]->APP_UID,
            'APP_NUMBER' => $application[3]->APP_NUMBER,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'DEL_DELEGATE_DATE' => '2021-05-23 09:52:32'
        ]);
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setUserUid($user->USR_ID);

        $res = $draft->getCountersByRange();
        $this->assertCount(4, $res);

        $res = $draft->getCountersByRange(null, null, null, 'month');
        $this->assertCount(1, $res);

        $res = $draft->getCountersByRange(null, null, null, 'year');
        $this->assertCount(1, $res);

        $res = $draft->getCountersByRange($process2->PRO_ID);
        $this->assertCount(1, $res);

        $res = $draft->getCountersByRange(null, '2021-05-20', '2021-05-22');
        $this->assertCount(3, $res);
    }

    /**
     * This tests the getCustomListCount() method.
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getCustomListCount()
     * @test
     */
    public function it_should_test_getCustomListCount_method()
    {
        $cases = $this->createManyDraft(3);

        $additionalTables = factory(AdditionalTables::class)->create();
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
            'CAL_TYPE' => 'draft',
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'USR_ID' => $cases->USR_ID
        ]);

        $draft = new Draft();
        $draft->setUserId($cases->USR_ID);
        $draft->setUserUid($cases->USR_UID);

        $res = $draft->getCustomListCount($caseList->CAL_ID, 'draft');

        //assertions
        $this->assertArrayHasKey('label', $res);
        $this->assertArrayHasKey('name', $res);
        $this->assertArrayHasKey('description', $res);
        $this->assertArrayHasKey('tableName', $res);
        $this->assertArrayHasKey('total', $res);

        $this->assertEquals($additionalTables->ADD_TAB_NAME, $res['tableName']);
        $this->assertEquals(3, $res['total']);
    }

    /**
     * This tests the getCasesRisk() method with on time filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getCasesRisk()
     * @test
     */
    public function it_tests_get_cases_risk_on_time()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff1Day = new DateInterval('P1D');
        $diff2Days = new DateInterval('P2D');
        $process = factory(Process::class)->create();
        $user = factory(User::class)->create();
        $application = factory(Application::class, 14)->states('draft')->create([
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $del = factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[0]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[0]->APP_UID,
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $date->add($diff1Day),
            'DEL_TASK_DUE_DATE' => $date->add($diff2Days)
        ]);
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setUserUid($user->USR_ID);
        $res = $draft->getCasesRisk($process->PRO_ID, $currentDate, $currentDate, 'ON_TIME', 10);
        $this->assertCount(1, $res);
    }

    /**
     * This tests the getCasesRisk() method with at risk filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getCasesRisk()
     * @test
     */
    public function it_tests_get_cases_risk_at_risk()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff2Days = new DateInterval('P2D');
        $process = factory(Process::class)->create();
        $user = factory(User::class)->create();
        $application = factory(Application::class, 14)->states('draft')->create([
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[0]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[0]->APP_UID,
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $currentDate,
            'DEL_TASK_DUE_DATE' => $date->add($diff2Days)
        ]);
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setUserUid($user->USR_ID);
        $res = $draft->getCasesRisk($process->PRO_ID, null, null, 'AT_RISK');
        $this->assertCount(1, $res);
    }

    /**
     * This tests the getCasesRisk() method with overdue filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getCasesRisk()
     * @test
     */
    public function it_tests_get_cases_risk_overdue()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff2Days = new DateInterval('P2D');
        $process = factory(Process::class)->create();
        $user = factory(User::class)->create();
        $application = factory(Application::class, 14)->states('draft')->create([
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application[0]->APP_INIT_USER,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application[0]->APP_UID,
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $currentDate,
            'DEL_TASK_DUE_DATE' => $date->sub($diff2Days)
        ]);
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setUserUid($user->USR_ID);
        $res = $draft->getCasesRisk($process->PRO_ID, null, null, 'OVERDUE');
        $this->assertCount(1, $res);
    }

    /**
     * This tests the getCounterMetrics() method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getCounterMetrics()
     * @test
     */
    public function it_should_test_get_counter_metrics()
    {
        $this->createDraft();
        $draft = new Draft();
        $result = $draft->getCounterMetrics();
        $this->assertTrue($result > 0);
    }
}
