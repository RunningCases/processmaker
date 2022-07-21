<?php

namespace Tests\Perfomance\workflow\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\AppAssignSelfServiceValue;
use ProcessMaker\Model\AppAssignSelfServiceValueGroup;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\GroupUser;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use ProcessMaker\Model\User;
use Tests\TestCase;

class DelegationTest extends TestCase
{
    use DatabaseTransactions;

    //This parameter is used for performance test, It has to be divisible by 4, because we have 4 types of self-service
    private $totalCases;
    //This parameter is used for performance test, It the maximum time in seconds
    private $maximumExecutionTime;

    /**
     * Define values of some parameters of the test
     */
    public function setUp(): void
    {
        if (!env('RUN_MYSQL_PERFORMANCE_TESTS')) {
            $this->markTestSkipped('Test related to the performance are disabled for this server configuration');
        } else {
            $this->totalCases = (int)env('TOTAL_CASES', 120);
            $this->maximumExecutionTime = (int)env('MAX_EXECUTION_TIME', 60);
        }
    }

    /**
     * This checks the counters is working properly in self-service user assigned
     * @covers Delegation::countSelfService
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_user_assigned()
    {
        //Define the total of cases to create
        $total = $this->totalCases;
        //Define the maximum time of execution
        $maximumTime = $this->maximumExecutionTime;
        //Create process
        $process = Process::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a task self service
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in delegation relate to self-service
        Delegation::factory($total)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $timeStart = microtime(true);
        Delegation::countSelfService($user->USR_UID);
        $timeEnd = microtime(true);
        $time = $timeEnd - $timeStart;
        //Compare if the time of execution is minor than the time defined in the .env
        $this->assertLessThan($maximumTime, $time);
        error_log('it_should_count_cases_by_user_with_self_service_user_assigned took [' . $total . ']--->' . $time);
    }

    /**
     * This checks the counters is working properly in self-service-value-based when the variable has a value related with the USR_UID
     * When the value assigned in the variable @@ARRAY_OF_USERS = [USR_UID]
     * @covers Delegation::countSelfService
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_value_based_usr_uid()
    {
        //Define the total of cases to create
        $total = $this->totalCases;
        //Define the maximum time of execution
        $maximumTime = $this->maximumExecutionTime;
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a task self service value based
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory($total)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $timeStart = microtime(true);
        Delegation::countSelfService($user->USR_UID);
        $timeEnd = microtime(true);
        $time = $timeEnd - $timeStart;
        //Compare if the time of execution is minor than the time defined in the .env
        $this->assertLessThan($maximumTime, $time);
        error_log('it_should_count_cases_by_user_with_self_service_value_based_usr_uid took [' . $total . ']--->' . $time);
    }

    /**
     * This checks the counters is working properly in self-service and self-service value based
     * @covers Delegation::countSelfService
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_mixed_with_self_service_value_based()
    {
        //Define the total of cases to create
        $total = $this->totalCases;
        //Define the maximum time of execution
        $maximumTime = $this->maximumExecutionTime;
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a task self service
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in self service
        Delegation::factory($total / 2)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create a task self service value based
        $task1 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task1->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self service value based
        Delegation::factory($total / 2)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $timeStart = microtime(true);
        Delegation::countSelfService($user->USR_UID);
        $timeEnd = microtime(true);
        $time = $timeEnd - $timeStart;
        //Compare if the time of execution is minor than the time defined in the .env
        $this->assertLessThan($maximumTime, $time);
        error_log('it_should_count_cases_by_user_with_self_service_mixed_with_self_service_value_based took [' . $total . ']--->' . $time);
    }

    /**
     * This checks the counters is working properly in self-service group assigned
     * @covers Delegation::countSelfService
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_group_assigned()
    {
        //Define the total of cases to create
        $total = $this->totalCases;
        //Define the maximum time of execution
        $maximumTime = $this->maximumExecutionTime;
        //Create process
        $process = Process::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        //Create a task self service
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the register in self-service
        Delegation::factory($total)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $timeStart = microtime(true);
        Delegation::countSelfService($user->USR_UID);
        $timeEnd = microtime(true);
        $time = $timeEnd - $timeStart;
        //Compare if the time of execution is minor than the time defined in the .env
        $this->assertLessThan($maximumTime, $time);
        error_log('it_should_count_cases_by_user_with_self_service_group_assigned took [' . $total . ']--->' . $time);
    }

    /**
     * This checks the counters is working properly in self-service-value-based when the variable has a value related with the GRP_UID
     * When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID]
     * @covers Delegation::countSelfService
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_value_based_grp_uid()
    {
        //Define the total of cases to create
        $total = $this->totalCases;
        //Define the maximum time of execution
        $maximumTime = $this->maximumExecutionTime;
        //Create process
        $process = Process::factory()->create();
        //Create a task self service value based
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create a case
        $application = Application::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create([
            'USR_USERNAME' => 'gary',
            'USR_LASTNAME' => 'Gary',
            'USR_FIRSTNAME' => 'Bailey',
        ]);
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $group->GRP_UID,
            'ASSIGNEE_ID' => $group->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory($total)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $timeStart = microtime(true);
        Delegation::countSelfService($user->USR_UID);
        $timeEnd = microtime(true);
        $time = $timeEnd - $timeStart;
        //Compare if the time of execution is minor than the time defined in the .env
        $this->assertLessThan($maximumTime, $time);
        error_log('it_should_count_cases_by_user_with_self_service_value_based_grp_uid took [' . $total . ']--->' . $time);
    }

    /**
     * This checks the counters is working properly in self-service user and group assigned in parallel task
     * @covers Delegation::countSelfService
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_user_and_group_assigned_parallel_task()
    {
        //Define the total of cases to create
        $total = $this->totalCases;
        //Define the maximum time of execution
        $maximumTime = $this->maximumExecutionTime;
        //Create process
        $process = Process::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        //Create a task self service
        $task1 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task1
        TaskUser::factory()->create([
            'TAS_UID' => $task1->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task2 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task2
        TaskUser::factory()->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task3 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task3->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task4 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task4->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the register in self-service related to the task1
        Delegation::factory($total / 4)->create([
            'TAS_ID' => $task1->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task2
        Delegation::factory($total / 4)->create([
            'TAS_ID' => $task2->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task3
        Delegation::factory($total / 4)->create([
            'TAS_ID' => $task3->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task4
        Delegation::factory($total / 4)->create([
            'TAS_ID' => $task4->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $timeStart = microtime(true);
        Delegation::countSelfService($user->USR_UID);
        $timeEnd = microtime(true);
        $time = $timeEnd - $timeStart;
        //Compare if the time of execution is minor than the time defined in the .env
        $this->assertLessThan($maximumTime, $time);
        error_log('it_should_count_cases_by_user_with_self_service_user_and_group_assigned_parallel_task took [' . $total . ']--->' . $time);
    }

    /**
     * This checks the counters is working properly in self-service-value-based with GRP_UID and USR_UID in parallel task
     * When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID, USR_UID]
     * @covers Delegation::countSelfService
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_value_based_usr_uid_and_grp_uid()
    {
        //Define the total of cases to create
        $total = $this->totalCases;
        //Define the maximum time of execution
        $maximumTime = $this->maximumExecutionTime;
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a task1 self service value based
        $task1 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task1->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory($total / 2)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task1->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create a task2 self service value based
        $task2 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task2->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory($total / 2)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task2->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $timeStart = microtime(true);
        Delegation::countSelfService($user->USR_UID);
        $timeEnd = microtime(true);
        $time = $timeEnd - $timeStart;
        //Compare if the time of execution is minor than the time defined in the .env
        $this->assertLessThan($maximumTime, $time);
        error_log('it_should_count_cases_by_user_with_self_service_value_based_usr_uid_and_grp_uid took [' . $total . ']--->' . $time);
    }
}