<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use ProcessMaker\BusinessModel\Cases\Supervising;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\GroupUser;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessUser;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Supervising
 */
class SupervisingTest extends TestCase
{

    /**
     * Tests the getData() method when the user is a supervisor of the process(es)
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @test
     */
    public function it_should_test_the_get_data_method_when_the_user_is_supervisor()
    {
        // Create process
        $process = factory(Process::class)->create();

        // Create user
        $user = factory(User::class)->create();

        // Create a task
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'NORMAL',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
        ]);
        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'NORMAL',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
        ]);

        // Create 3 cases
        $app1 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $app2 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $app3 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);

        // Create the registers in delegation
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app1['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app1['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app2['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app2['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app3['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app3['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        // Create the register in the ProcessUser table
        factory(ProcessUser::class)->create(
            [
                'PRO_UID' => $process->PRO_UID,
                'USR_UID' => $user->USR_UID,
                'PU_TYPE' => 'SUPERVISOR'
            ]
        );

        // Instance the Supervising class
        $Supervising = new Supervising();

        // Set the user UID
        $Supervising->setUserUid($user->USR_UID);

        // Set the user ID
        $Supervising->setUserId($user->USR_ID);

        // Call the getData method
        $res = $Supervising->getData();

        // Asserts the result contains 3 registers
        $this->assertCount(3, $res);
    }

    /**
     * Tests the getData() method when the user belongs to a group supervisor
     * 
     * covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @test
     */
    public function it_should_test_the_get_data_method_when_the_user_belong_to_a_group_supervisor()
    {
        //Create process
        $process = factory(Process::class)->create();

        //Create user
        $user = factory(User::class)->create();

        $groupUser = factory(GroupUser::class)->create([
            'USR_UID' => $user['USR_UID']
        ]);

        //Create a task
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
        ]);
        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'NORMAL',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
        ]);

        $app1 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $app2 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $app3 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);

        //Create the register in delegation
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app1['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app1['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app2['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app2['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app3['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app3['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        //Create the register in the ProcessUser table
        factory(ProcessUser::class)->create(
            [
                'PRO_UID' => $process->PRO_UID,
                'USR_UID' => $groupUser->USR_UID,
                'PU_TYPE' => 'SUPERVISOR'
            ]
        );

        // Instance the Supervising object
        $Supervising = new Supervising();

        //Set the user UID
        $Supervising->setUserUid($user->USR_UID);

        //Set the user ID
        $Supervising->setUserId($user->USR_ID);

        //Call the getData method
        $res = $Supervising->getData();

        // Asserts the result contains 3 registers
        $this->assertCount(3, $res);
    }

    /**
     * Tests the getData() method when the user is not a supervisor neither belongs to a group supervisor 
     * 
     * covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @test
     */
    public function it_should_test_the_get_data_method_when_the_user_is_not_supervisor()
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
        ]);

        $app1 = factory(Application::class)->states('todo')->create();
        $app2 = factory(Application::class)->states('todo')->create();
        $app3 = factory(Application::class)->states('todo')->create();

        //Create the register in delegation
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'APP_NUMBER' => $app1['APP_NUMBER']
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'APP_NUMBER' => $app2['APP_NUMBER']
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'APP_NUMBER' => $app3['APP_NUMBER']
        ]);

        // Instance the Supervising object
        $Supervising = new Supervising();

        //Set the user UID
        $Supervising->setUserUid($user->USR_UID);

        //Set the user ID
        $Supervising->setUserId($user->USR_ID);

        //Call the getData method
        $res = $Supervising->getData();


        // Asserts the result
        $this->assertEmpty($res);
    }

    /**
     * Tests the getCounter() method
     * 
     * covers \ProcessMaker\BusinessModel\Cases\Supervising::getCounter()
     * @test
     */
    public function it_should_count_the_data()
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
        ]);

        //Create 3 cases
        $app1 = factory(Application::class)->states('todo')->create();
        $app2 = factory(Application::class)->states('todo')->create();
        $app3 = factory(Application::class)->states('todo')->create();

        //Create the registers in delegation
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'APP_NUMBER' => $app1['APP_NUMBER']
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'APP_NUMBER' => $app2['APP_NUMBER']
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'APP_NUMBER' => $app3['APP_NUMBER']
        ]);

        //Create the register in the ProcessUser table
        factory(ProcessUser::class)->create(
            [
                'PRO_UID' => $process->PRO_UID,
                'USR_UID' => $user->USR_UID,
                'PU_TYPE' => 'SUPERVISOR'
            ]
        );

        // Instance the Supervising object
        $Supervising = new Supervising();

        //Set the user UID
        $Supervising->setUserUid($user->USR_UID);

        //Set the user ID
        $Supervising->setUserId($user->USR_ID);

        //Call the getCounter method
        $res = $Supervising->getCounter();

        //Assert the counter
        $this->assertEquals(3, $res);
    }

    /**
     * Tests the filter by APP_NUMBER
     * 
     * covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @test
     */
    public function it_filters_by_app_number()
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
        ]);

        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'NORMAL',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
        ]);

        $app1 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $app2 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $app3 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);

        //Create the register in delegation
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app1['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app1['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app2['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app2['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app3['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app3['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        //Create the register in the ProcessUser table
        factory(ProcessUser::class)->create(
            [
                'PRO_UID' => $process->PRO_UID,
                'USR_UID' => $user->USR_UID,
                'PU_TYPE' => 'SUPERVISOR'
            ]
        );

        // Instance the Supervising object
        $Supervising = new Supervising();

        //Set the user UID
        $Supervising->setUserUid($user->USR_UID);

        //Set the user ID
        $Supervising->setUserId($user->USR_ID);

        $Supervising->setCaseNumber($app3['APP_NUMBER']);

        //Call the getData method
        $res = $Supervising->getData();

        // Asserts the result contains 3 registers
        $this->assertCount(1, $res);
        //Asserts that the result contains the app number searched
        $this->assertContains($app3['APP_NUMBER'], $res[0]);
    }

    /**
     * Tests the filter by process
     * 
     * covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @test
     */
    public function it_filters_by_process()
    {
        //Create process
        $process = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();

        //Create user
        $user = factory(User::class)->create();

        //Create a task
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
        ]);

        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'NORMAL',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
        ]);

        $app1 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $app2 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $app3 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process2->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);

        //Create the register in delegation
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app1['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app1['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app2['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app2['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $app3['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $app3['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        //Create the register in the ProcessUser table
        factory(ProcessUser::class)->create(
            [
                'PRO_UID' => $process->PRO_UID,
                'USR_UID' => $user->USR_UID,
                'PU_TYPE' => 'SUPERVISOR'
            ]
        );

        factory(ProcessUser::class)->create(
            [
                'PRO_UID' => $process2->PRO_UID,
                'USR_UID' => $user->USR_UID,
                'PU_TYPE' => 'SUPERVISOR'
            ]
        );

        // Instance the Supervising object
        $Supervising = new Supervising();

        //Set the user UID
        $Supervising->setUserUid($user->USR_UID);

        //Set the user ID
        $Supervising->setUserId($user->USR_ID);

        //Set the process Id filter
        $Supervising->setProcessId($process2['PRO_ID']);

        //Call the getData method
        $res = $Supervising->getData();

        $this->assertCount(1, $res);
    }

    /**
     * Tests the order by value
     * 
     * covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @test
     */
    public function it_orders_the_query_by_column()
    {
        //Create process
        $process = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();

        //Create user
        $user = factory(User::class)->create();

        //Create a task
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
        ]);

        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'NORMAL',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
        ]);

        $app1 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $app2 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $app3 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process2->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);

        //Create the register in delegation
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app1['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app1['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app2['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app2['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $app3['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $app3['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        //Create the register in the ProcessUser table
        factory(ProcessUser::class)->create(
            [
                'PRO_UID' => $process->PRO_UID,
                'USR_UID' => $user->USR_UID,
                'PU_TYPE' => 'SUPERVISOR'
            ]
        );

        factory(ProcessUser::class)->create(
            [
                'PRO_UID' => $process2->PRO_UID,
                'USR_UID' => $user->USR_UID,
                'PU_TYPE' => 'SUPERVISOR'
            ]
        );

        // Instance the Supervising object
        $Supervising = new Supervising();

        //Set the user UID
        $Supervising->setUserUid($user->USR_UID);

        //Set the user ID
        $Supervising->setUserId($user->USR_ID);

        //Set the orderby value
        $Supervising->setOrderByColumn('APPLICATION.APP_NUMBER');

        //Call the getData method
        $res = $Supervising->getData();

        $this->assertCount(3, $res);
        $this->assertTrue($res[0]['APP_NUMBER'] > $res[1]['APP_NUMBER']);
    }

    /**
     * Tests the limit in the order by clausule
     * 
     * covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @test
     */
    public function it_set_the_limit_in_the_order_by_clausule()
    {
        //Create process
        $process = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();

        //Create user
        $user = factory(User::class)->create();

        //Create a task
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
        ]);

        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'NORMAL',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
        ]);

        $app1 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $app2 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);
        $app3 = factory(Application::class)->states('todo')->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process2->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
        ]);

        //Create the register in delegation
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app1['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app1['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app1['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app2['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app2['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'APP_NUMBER' => $app2['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $app3['APP_NUMBER'],
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' =>0
        ]);
        factory(Delegation::class, 1)->create([
            "APP_UID" => $app3['APP_UID'],
            'TAS_ID' => $task2->TAS_ID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $app3['APP_NUMBER'],
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' =>1
        ]);

        //Create the register in the ProcessUser table
        factory(ProcessUser::class)->create(
            [
                'PRO_UID' => $process->PRO_UID,
                'USR_UID' => $user->USR_UID,
                'PU_TYPE' => 'SUPERVISOR'
            ]
        );

        factory(ProcessUser::class)->create(
            [
                'PRO_UID' => $process2->PRO_UID,
                'USR_UID' => $user->USR_UID,
                'PU_TYPE' => 'SUPERVISOR'
            ]
        );

        // Instance the Supervising object
        $Supervising = new Supervising();

        //Set the user UID
        $Supervising->setUserUid($user->USR_UID);

        //Set the user ID
        $Supervising->setUserId($user->USR_ID);

        //Set the limit value
        $Supervising->setLimit(1);

        //Call the getData method
        $res = $Supervising->getData();

        $this->assertCount(1, $res);
    }
}