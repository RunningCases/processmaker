<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
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
 * @coversDefaultClass ProcessMaker\BusinessModel\Cases\Paused
 * @package Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases
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
     * It tests the getData method without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @test
     */
    public function it_should_test_get_data_method_without_filters()
    {

        // Create factories related to the to_do cases
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
     * It tests the getData method with app number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @test
     */
    public function it_should_test_get_data_by_case_number()
    {
        // Create factories related to the to_do cases
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
     * @test
     */
    public function it_should_test_get_data_by_task_filter()
    {
        // Create factories related to the to_do cases
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
     * It tests the getData method using OrderBy Case Number
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @test
     */
    public function it_should_test_get_data_by_process_filter()
    {
        // Create factories related to the to_do cases
        $cases = $this->createPaused();
        // Create new Paused object
        $paused = new Paused();
        // Set the user UID
        $paused->setUserUid($cases->USR_UID);
        // Set the user ID
        $paused->setUserId($cases->USR_ID);
        $paused->setProcessId($cases->PRO_ID);
        // Call to getData method
        $res = $paused->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method using OrderBy
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Paused::getData()
     * @test
     */
    public function it_should_test_get_data_by_case_title()
    {
        // Create factories related to the to_do cases
        $cases = $this->createPaused();
        // Create new Inbox object
        $paused = new Paused();
        $paused->setUserUid($cases->USR_UID);
        $paused->setUserId($cases->USR_ID);
        $res = $paused->getData();
        $this->assertNotEmpty($res);
    }
}
