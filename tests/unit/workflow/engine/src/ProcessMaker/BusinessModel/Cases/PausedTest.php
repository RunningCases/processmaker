<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use DateInterval;
use Datetime;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Paused;
use ProcessMaker\Model\AdditionalTables;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\AppDelay;
use ProcessMaker\Model\CaseList;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class PausedTest
 *
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Paused
 */
class PausedTest extends TestCase
{
    /**
     * Method set up.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->truncateNonInitialModels();
    }

    /**
     * Create paused cases factories
     *
     * @param string
     *
     * @return array
     */
    public function createPaused()
    {
        //Create processes
        $process1 = Process::factory()->create(
            ['PRO_CATEGORY' => '1']
        );
        $process2 = Process::factory()->create(
            ['PRO_CATEGORY' => '2']
        );

        //Create user
        $user = User::factory()->create();

        //Create a task
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);

        $application1 = Application::factory()->create();
        $application2 = Application::factory()->create();

        //Create the register in delegation
        Delegation::factory()->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1
        ]);
        $delegation1 = Delegation::factory()->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2
        ]);

        Delegation::factory()->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1
        ]);
        $delegation2 = Delegation::factory()->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2
        ]);

        //Create the registers in AppDelay
        AppDelay::factory(5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        //Create the registers in AppDelay
        AppDelay::factory(5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation2->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation2->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);

        return $delegation2;
    }

    /**
     * Create many paused cases for one user
     * 
     * @param int
     * @return object
     */
    public function createMultiplePaused($cases, $category = 1, $user = null)
    {
        if (is_null($user)) {
            $user = User::factory()->create();
        }

        for ($i = 0; $i < $cases; $i = $i + 1) {
            $process1 = Process::factory()->create(
                ['PRO_CATEGORY' => 1, 'CATEGORY_ID' => $category]
            );

            $task = Task::factory()->create([
                'TAS_ASSIGN_TYPE' => '',
                'TAS_GROUP_VARIABLE' => '',
                'PRO_UID' => $process1->PRO_UID,
                'TAS_TYPE' => 'NORMAL'
            ]);

            $application1 = Application::factory()->create();

            Delegation::factory()->create([
                'APP_UID' => $application1->APP_UID,
                'APP_NUMBER' => $application1->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'DEL_THREAD_STATUS' => 'CLOSED',
                'USR_UID' => $user->USR_UID,
                'USR_ID' => $user->USR_ID,
                'PRO_ID' => $process1->PRO_ID,
                'PRO_UID' => $process1->PRO_UID,
                'DEL_PREVIOUS' => 0,
                'DEL_INDEX' => 1
            ]);
            $delegation1 = Delegation::factory()->create([
                'APP_UID' => $application1->APP_UID,
                'APP_NUMBER' => $application1->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_UID' => $user->USR_UID,
                'USR_ID' => $user->USR_ID,
                'PRO_ID' => $process1->PRO_ID,
                'PRO_UID' => $process1->PRO_UID,
                'DEL_PREVIOUS' => 1,
                'DEL_INDEX' => 2
            ]);

            AppDelay::factory()->create([
                'APP_DELEGATION_USER' => $user->USR_UID,
                'PRO_UID' => $process1->PRO_UID,
                'APP_NUMBER' => $delegation1->APP_NUMBER,
                'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
                'APP_DISABLE_ACTION_USER' => 0,
                'APP_TYPE' => 'PAUSE'
            ]);
        }
        return $delegation1;
    }

    /**
     * It tests the getData method without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getColumnsView()
     * @covers \ProcessMaker\Model\Delegation::scopePaused()
     * @test
     */
    public function it_get_result_without_filters()
    {
        // Create factories related to the paused cases
        $cases = $this->createPaused();
        // Create new Paused object
        $paused = new Paused();
        // Set the user UID
        $paused->setUserUid($cases->USR_UID);
        // Set the user ID
        $paused->setUserId($cases->USR_ID);
        // Call to getData method
        $res = $paused->getData();
        // This assert that the expected numbers of results are returned with no filters
        $this->assertEquals(10, count($res));
    }

    /**
     * It tests the getData method with case number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::filters()
     * @test
     */
    public function it_filter_by_app_number()
    {
        // Create factories related to the paused cases
        $cases = $this->createPaused();
        //Create new Paused object
        $paused = new Paused();
        //Set the user UID
        $paused->setUserUid($cases->USR_UID);
        //Set the user ID
        $paused->setUserId($cases->USR_ID);
        //Set app number
        $paused->setCaseNumber($cases->APP_NUMBER);
        //Call to getData method
        $res = $paused->getData();
        //This asserts there are results for the filtered app number
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with case number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::filters()
     * @test
     */
    public function it_filter_by_specific_cases()
    {
        // Create factories related to the paused cases
        $cases = $this->createPaused();
        //Create new Paused object
        $paused = new Paused();
        //Set the user UID
        $paused->setUserUid($cases->USR_UID);
        //Set the user ID
        $paused->setUserId($cases->USR_ID);
        //Set app number
        $paused->setCasesNumbers([$cases->APP_NUMBER]);
        //Call to getData method
        $res = $paused->getData();
        //This asserts there are results for the filtered app number
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with taskId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::filters()
     * @test
     */
    public function it_filter_by_task()
    {
        // Create factories related to the paused cases
        $cases = $this->createPaused();
        // Create new Paused object
        $paused = new Paused();
        // Set the user UID
        $paused->setUserUid($cases->USR_UID);
        // Set the user ID
        $paused->setUserId($cases->USR_ID);
        // Set taskId
        $paused->setTaskId($cases->TAS_ID);
        // Call to getData method
        $res = $paused->getData();
        // This asserts there are results for the filtered task
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with categoryId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::filters()
     * @test
     */
    public function it_filter_by_category()
    {
        // Create factories related to the paused cases
        $cases = $this->createPaused();
        // Create new Paused object
        $paused = new Paused();
        // Set the user UID
        $paused->setUserUid($cases->USR_UID);
        // Set the user ID
        $paused->setUserId($cases->USR_ID);
        $paused->setCategoryId(2000);
        // Get the data
        $res = $paused->getData();
        // Asserts
        $this->assertEmpty($res);
    }

    /**
     * It tests the getData method with processId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::filters()
     * @test
     */
    public function it_filter_by_process()
    {
        // Create factories related to the paused cases
        $cases = $this->createPaused();
        // Create new Paused object
        $paused = new Paused();
        // Set the user UID
        $paused->setUserUid($cases->USR_UID);
        // Set the user ID
        $paused->setUserId($cases->USR_ID);
        $paused->setProcessId($cases->PRO_ID);
        // Get the data
        $res = $paused->getData();
        // Asserts
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with case title filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::filters()
     * @test
     */
    public function it_filter_by_thread_title()
    {
        // Create factories related to the paused cases
        $cases = $this->createPaused();
        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();
        // Create new Paused object
        $paused = new Paused();
        $paused->setUserUid($cases->USR_UID);
        $paused->setUserId($cases->USR_ID);
        // Set the title
        $paused->setCaseTitle($cases->DEL_TITLE);
        $res = $paused->getData();
        $this->assertTrue(!empty($res));
    }

    /**
     * It tests the getData method with setDelegateFrom and setDelegateTo filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::filters()
     * @test
     */
    public function it_filter_by_delegate_from_to()
    {
        // Create factories related to the paused cases
        $cases = $this->createPaused();
        // Create new Paused object
        $paused = new Paused();
        $paused->setUserUid($cases->USR_UID);
        $paused->setUserId($cases->USR_ID);
        $paused->setDelegateFrom($cases->DEL_DELEGATE_DATE->format("Y-m-d"));
        $paused->setDelegateTo($cases->DEL_DELEGATE_DATE->format("Y-m-d"));
        // Get data
        $res = $paused->getData();
        $this->assertEmpty($res);
    }


    /**
     * It tests the getData method with send by filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::setSendBy()
     * @test
     */
    public function it_filter_send_by()
    {
        // Create factories related to the to_do cases
        $cases = $this->createPaused();
        // Create new Paused object
        $paused = new Paused();
        $paused->setUserId($cases->USR_ID);
        $paused->setSendBy($cases->USR_ID);
        $res = $paused->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getCounter() method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getCounter()
     * @test
     */
    public function it_test_count()
    {
        $cases = $this->createMultiplePaused(3);
        $paused = new Paused();
        $paused->setUserId($cases->USR_ID);
        $paused->setUserUid($cases->USR_UID);

        $res = $paused->getCounter();
        $this->assertEquals(3, $res);
    }

    /**
     * It tests the getPagingCounters() method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getPagingCounters()
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::filters()
     * @test
     */
    public function it_should_test_get_paging_counters_method()
    {
        $cases = $this->createMultiplePaused(3);
        $paused = new Paused();
        $paused->setUserId($cases->USR_ID);
        $paused->setUserUid($cases->USR_UID);

        $res = $paused->getPagingCounters();
        $this->assertEquals(3, $res);

        $delegation = Delegation::select()->where('USR_ID', $cases->USR_ID)->first();

        $paused->setCaseNumber($delegation->APP_NUMBER);
        $paused->setProcessId($delegation->PRO_ID);
        $paused->setTaskId($delegation->TAS_ID);
        $paused->setCaseUid($delegation->APP_UID);

        $res = $paused->getPagingCounters();
        $this->assertEquals(1, $res);
    }

    /**
     * It tests the getCountersByProcesses() method without filters
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_no_filter()
    {
        $cases = $this->createMultiplePaused(2);
        $paused = new Paused();
        $paused->setUserId($cases->USR_ID);
        $paused->setUserUid($cases->USR_ID);
        $res = $paused->getCountersByProcesses();
        $this->assertCount(2, $res);
    }

    /**
     * It tests the getCountersByProcesses() method with the category filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_category()
    {
        $user = User::factory()->create();
        $this->createMultiplePaused(3, 2, $user);
        $this->createMultiplePaused(2, 3, $user);
        $paused = new Paused();
        $paused->setUserId($user->USR_ID);
        $paused->setUserUid($user->USR_ID);
        $res = $paused->getCountersByProcesses(2);
        $this->assertCount(3, $res);
    }

    /**
     * It tests the getCountersByProcesses() method with the top ten filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_top_ten()
    {
        $user = User::factory()->create();
        $this->createMultiplePaused(20, 2, $user);
        $paused = new Paused();
        $paused->setUserId($user->USR_ID);
        $paused->setUserUid($user->USR_UID);
        $res = $paused->getCountersByProcesses(null, true);
        $this->assertCount(10, $res);
    }

    /**
     * It tests the getCountersByProcesses() method with the processes filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_processes_filter()
    {
        $user = User::factory()->create();
        $process1 = Process::factory()->create();

        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);

        $application1 = Application::factory()->create();

        Delegation::factory()->create([
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1
        ]);
        $delegation1 = Delegation::factory()->create([
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2
        ]);

        AppDelay::factory()->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process1->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        $this->createMultiplePaused(3, 2, $user);
        $paused = new Paused();
        $paused->setUserId($user->USR_ID);
        $paused->setUserUid($user->USR_UID);
        $res = $paused->getCountersByProcesses(null, false, [$process1->PRO_ID]);
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCountersByRange() method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getCountersByRange()
     * @test
     */
    public function it_should_test_get_counters_by_range_method()
    {
        $user = User::factory()->create();
        $process1 = Process::factory()->create();
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);

        $application1 = Application::factory()->create();
        Delegation::factory()->create([
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1
        ]);
        $delegation1 = Delegation::factory()->create([
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
            'DEL_DELEGATE_DATE' => '2021-05-23 00:00:00'
        ]);
        AppDelay::factory()->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process1->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);

        $application2 = Application::factory()->create();
        Delegation::factory()->create([
            'APP_UID' => $application2->APP_UID,
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1
        ]);
        $delegation2 = Delegation::factory()->create([
            'APP_UID' => $application2->APP_UID,
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
            'DEL_DELEGATE_DATE' => '2021-05-24 09:52:32'
        ]);
        AppDelay::factory()->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process1->PRO_UID,
            'APP_NUMBER' => $delegation2->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation2->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        $paused = new Paused();
        $paused->setUserId($user->USR_ID);
        $paused->setUserUid($user->USR_UID);

        $res = $paused->getCountersByRange();
        $this->assertCount(2, $res);

        $res = $paused->getCountersByRange(null, null, null, 'month');
        $this->assertCount(1, $res);

        $res = $paused->getCountersByRange(null, null, null, 'year');
        $this->assertCount(1, $res);

        $res = $paused->getCountersByRange($process1->PRO_ID);
        $this->assertCount(2, $res);

        $res = $paused->getCountersByRange(null, '2021-05-20', '2021-05-23');
        $this->assertCount(1, $res);
    }

    /**
     * This tests the getCustomListCount() method.
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getCustomListCount()
     * @test
     */
    public function it_should_test_getCustomListCounts_method()
    {
        $cases = $this->createMultiplePaused(3);

        $additionalTables = AdditionalTables::factory()->create([
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
            . "KEY `indexTable` (`APP_UID`)"
            . ")ENGINE=InnoDB  DEFAULT CHARSET='utf8'";
        DB::statement($query);

        $caseList = CaseList::factory()->create([
            'CAL_TYPE' => 'paused',
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'USR_ID' => $cases->USR_ID
        ]);

        $paused = new Paused();
        $paused->setUserId($cases->USR_ID);
        $paused->setUserUid($cases->USR_UID);

        $res = $paused->getCustomListCount($caseList->CAL_ID, 'paused');

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
     * It tests the getCasesRisk() method with the ontime filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getCasesRisk()
     * @test
     */
    public function it_should_test_get_cases_risk_on_time()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff1Day = new DateInterval('P1D');
        $diff2Days = new DateInterval('P2D');
        $user = User::factory()->create();
        $process1 = Process::factory()->create();

        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);

        $application1 = Application::factory()->create();

        Delegation::factory()->create([
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $date->add($diff1Day),
            'DEL_TASK_DUE_DATE' => $date->add($diff2Days)
        ]);
        $delegation1 = Delegation::factory()->create([
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $date->add($diff1Day),
            'DEL_TASK_DUE_DATE' => $date->add($diff2Days)
        ]);

        AppDelay::factory()->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process1->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        $this->createMultiplePaused(3, 2, $user);
        $paused = new Paused();
        $paused->setUserId($user->USR_ID);
        $paused->setUserUid($user->USR_UID);
        $res = $paused->getCasesRisk($process1->PRO_ID);
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCasesRisk() method with the at risk filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getCasesRisk()
     * @test
     */
    public function it_should_test_get_cases_risk_at_risk()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff2Days = new DateInterval('P2D');
        $user = User::factory()->create();
        $process1 = Process::factory()->create();

        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);

        $application1 = Application::factory()->create();

        Delegation::factory()->create([
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $currentDate,
            'DEL_TASK_DUE_DATE' => $date->add($diff2Days)
        ]);
        $delegation1 = Delegation::factory()->create([
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $currentDate,
            'DEL_TASK_DUE_DATE' => $date->add($diff2Days)
        ]);

        AppDelay::factory()->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process1->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        $this->createMultiplePaused(3, 2, $user);
        $paused = new Paused();
        $paused->setUserId($user->USR_ID);
        $paused->setUserUid($user->USR_UID);
        $res = $paused->getCasesRisk($process1->PRO_ID, null, null, 'AT_RISK');
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCasesRisk() method with the overdue filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getCasesRisk()
     * @test
     */
    public function it_should_test_get_cases_risk_overdue()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff2Days = new DateInterval('P2D');
        $user = User::factory()->create();
        $process1 = Process::factory()->create();

        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);

        $application1 = Application::factory()->create();

        Delegation::factory()->create([
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $currentDate,
            'DEL_TASK_DUE_DATE' => $date->sub($diff2Days)
        ]);
        $delegation1 = Delegation::factory()->create([
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $currentDate,
            'DEL_TASK_DUE_DATE' => $date->sub($diff2Days)
        ]);

        AppDelay::factory()->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process1->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        $this->createMultiplePaused(3, 2, $user);
        $paused = new Paused();
        $paused->setUserId($user->USR_ID);
        $paused->setUserUid($user->USR_UID);
        $res = $paused->getCasesRisk($process1->PRO_ID, null, null, 'OVERDUE');
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCounterMetrics() method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getCounterMetrics()
     * @test
     */
    public function it_tests_get_counter_metrics()
    {
        $this->createMultiplePaused(3);
        $paused = new Paused();

        $res = $paused->getCounterMetrics();
        $this->assertTrue($res > 0);
    }
}
