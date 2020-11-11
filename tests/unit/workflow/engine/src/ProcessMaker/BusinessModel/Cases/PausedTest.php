<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

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
    /**
     * It tests the getData method without filters
     *
     * @covers ::getData()
     * @test
     */
    public function it_should_test_get_data_method_without_filters()
    {
        //Create process
        $process = factory(Process::class)->create();

        //Create user
        $user = factory(User::class)->create();

        //Create a task
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);

        $application = factory(Application::class)->create();
        //Create the register in delegation
        $delegation1 = factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1
        ]);

        $delegation2 = factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2
        ]);

        //Create the registers in AppDelay
        factory(AppDelay::class, 5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        //Create the registers in AppDelay
        factory(AppDelay::class, 5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $delegation2->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation2->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);

        //Create new Paused object
        $paused = new Paused();

        //Set the user UID
        $paused->setUserUid($user->USR_UID);

        //Set the user ID
        $paused->setUserId($user->USR_ID);

        //Call to getData method
        $res = $paused->getData();

        //This assert that the expected numbers of results are returned with no filters
        $this->assertEquals(10, count($res));
    }

    /**
     * It tests the getData method with app number filter
     *
     * @covers ::getData()
     * @test
     */
    public function it_should_test_get_data_method_with_app_number_filter()
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

        //Create new Paused object
        $paused = new Paused();

        //Set the user UID
        $paused->setUserUid($user->USR_UID);

        //Set the user ID
        $paused->setUserId($user->USR_ID);

        //Set app number
        $paused->setCaseNumber($delegation1->APP_NUMBER);

        //Call to getData method
        $res = $paused->getData();

        //This asserts there are results for the filtered app number
        $this->assertCount(5, $res);

        //This asserts the result corresponds to the app number filtered
        $this->assertEquals($delegation1->APP_NUMBER, $res[0]['APP_NUMBER']);

        //Set app number
        $paused->setCaseNumber($delegation2->APP_NUMBER);

        //Call to getData method
        $res = $paused->getData();

        //This asserts there are results for the filtered app number
        $this->assertCount(5, $res);

        //This asserts the result corresponds to the app number filtered
        $this->assertEquals($delegation2->APP_NUMBER, $res[0]['APP_NUMBER']);;
    }

    /**
     * It tests the getData method with taskId filter
     *
     * @covers ::getData()
     * @test
     */
    public function it_should_test_get_data_method_with_task_id_filter()
    {
        //Create processes
        $process1 = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();

        //Create user
        $user = factory(User::class)->create();

        //Create a task
        $task1 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
        ]);

        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process2->PRO_UID,
        ]);

        $application1 = factory(Application::class)->create();
        $application2 = factory(Application::class)->create();

        //Create the register in delegation
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task1->TAS_ID,
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
            'TAS_ID' => $task1->TAS_ID,
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
            'TAS_ID' => $task2->TAS_ID,
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
            'TAS_ID' => $task2->TAS_ID,
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

        //Create new Paused object
        $paused = new Paused();

        //Set the user UID
        $paused->setUserUid($user->USR_UID);

        //Set the user ID
        $paused->setUserId($user->USR_ID);

        //Set taskId
        $paused->setTaskId($task1->TAS_ID);

        //Call to getData method
        $res = $paused->getData();

        //This asserts there are results for the filtered task
        $this->assertCount(5, $res);

        //This asserts the result corresponds to the task filtered
        $this->assertEquals($task1->TAS_TITLE, $res[0]['TAS_TITLE']);

        //Set taskId
        $paused->setTaskId($task2->TAS_ID);

        //Call to getData method
        $res = $paused->getData();

        //This asserts there are results for the filtered task
        $this->assertCount(5, $res);

        //This asserts the result corresponds to the task filtered
        $this->assertEquals($task2->TAS_TITLE, $res[0]['TAS_TITLE']);
    }

    /**
     * It tests the getData method using OrderBy Case Number
     *
     * @covers ::getData()
     * @test
     */
    public function it_should_return_inbox_sort_by_case_number()
    {
        //Create processes
        $process1 = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();

        //Create user
        $user = factory(User::class)->create();

        //Create a task
        $task1 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
        ]);

        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process2->PRO_UID,
        ]);

        $application1 = factory(Application::class)->create();
        $application2 = factory(Application::class)->create();

        //Create the register in delegation
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task1->TAS_ID,
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
            'TAS_ID' => $task1->TAS_ID,
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
            'TAS_ID' => $task2->TAS_ID,
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
            'TAS_ID' => $task2->TAS_ID,
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

        //Create new Paused object
        $paused = new Paused();

        //Set the user UID
        $paused->setUserUid($user->USR_UID);

        //Set the user ID
        $paused->setUserId($user->USR_ID);

        //Set OrderBYColumn value
        $paused->setOrderByColumn('APP_DELEGATION.APP_NUMBER');

        //Set Order Direction value
        $paused->setOrderDirection('DESC');

        //Call to getData method
        $res = $paused->getData();

        //Asserts that the order is descending
        $this->assertGreaterThan($res[count($res) - 1]['APP_NUMBER'], $res[0]['APP_NUMBER']);

        //Set Order Direction value
        $paused->setOrderDirection('ASC');

        //Call to getData method
        $res = $paused->getData();

        //Asserts that the order is ascending
        $this->assertLessThan($res[count($res) - 1]['APP_NUMBER'], $res[0]['APP_NUMBER']);
    }

    /**
     * It tests the limit
     *
     * @covers ::getData()
     * @test
     */
    public function it_should_test_get_data_method_with_limit()
    {
        //Create processes
        $process1 = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();

        //Create user
        $user = factory(User::class)->create();

        //Create a task
        $task1 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
        ]);

        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process2->PRO_UID,
        ]);

        $application1 = factory(Application::class)->create();
        $application2 = factory(Application::class)->create();

        //Create the register in delegation
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task1->TAS_ID,
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
            'TAS_ID' => $task1->TAS_ID,
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
            'TAS_ID' => $task2->TAS_ID,
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
            'TAS_ID' => $task2->TAS_ID,
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

        //Create new Paused object
        $paused = new Paused();

        //Set the user UID
        $paused->setUserUid($user->USR_UID);

        //Set the user ID
        $paused->setUserId($user->USR_ID);

        //Set OrderBYColumn value
        $paused->setOrderByColumn('APP_DELEGATION.APP_NUMBER');

        //Set offset and limit values
        $paused->setOffset(0);
        $paused->setLimit(2);

        //Call to getData method
        $res = $paused->getData();

        //This assert that there are results with read inbox status
        $this->assertCount(2, $res);
    }
}
