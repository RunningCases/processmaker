<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Paused;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\AppDelay;
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
    use DatabaseTransactions;

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
        $process1 = factory(Process::class)->create(
            ['PRO_CATEGORY' => '1']
        );
        $process2 = factory(Process::class)->create(
            ['PRO_CATEGORY' => '2']
        );

        //Create user
        $user = factory(User::class)->create();

        //Create a task
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);

        $application1 = factory(Application::class)->create();
        $application2 = factory(Application::class)->create();

        //Create the register in delegation
        factory(Delegation::class)->create([
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
        $delegation1 = factory(Delegation::class)->create([
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

        factory(Delegation::class)->create([
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
        $delegation2 = factory(Delegation::class)->create([
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
        factory(AppDelay::class, 5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        //Create the registers in AppDelay
        factory(AppDelay::class, 5)->create([
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
    public function createMultiplePaused($cases)
    {
        $user = factory(\ProcessMaker\Model\User::class)->create();

        for ($i = 0; $i < $cases; $i = $i + 1) {
            $process1 = factory(Process::class)->create(
                ['PRO_CATEGORY' => '1']
            );

            $task = factory(Task::class)->create([
                'TAS_ASSIGN_TYPE' => '',
                'TAS_GROUP_VARIABLE' => '',
                'PRO_UID' => $process1->PRO_UID,
                'TAS_TYPE' => 'NORMAL'
            ]);

            $application1 = factory(Application::class)->create();

            factory(Delegation::class)->create([
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
            $delegation1 = factory(Delegation::class)->create([
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

            factory(AppDelay::class)->create([
                'APP_DELEGATION_USER' => $user->USR_UID,
                'PRO_UID' => $process1->PRO_UID,
                'APP_NUMBER' => $delegation1->APP_NUMBER,
                'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
                'APP_DISABLE_ACTION_USER' => 0,
                'APP_TYPE' => 'PAUSE'
            ]);
        }
        return $user;
    }

    /**
     * It tests the getData method without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
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
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
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
     * It tests the getData method with taskId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
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
     * It tests the getData method with processId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
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
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
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
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getPagingCounters() method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getPagingCounters()
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
}
