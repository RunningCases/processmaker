<?php

namespace Tests\unit\workflow\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Unassigned;
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

/**
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Unassigned
 */
class UnassignedTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Create unassigned cases factories
     *
     * @param string
     *
     * @return array
     */
    public function createSelfServiceUser()
    {
        // Create user`
        $user = factory(User::class)->create();
        for ($i = 1; $i <= 2; $i++) {
            //Create process
            $process = factory(Process::class)->create();
            //Create application
            $application = factory(Application::class)->create([
                'APP_STATUS_ID' => 2
            ]);
            //Create a task self service
            $task = factory(Task::class)->create([
                'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
                'TAS_GROUP_VARIABLE' => '',
                'PRO_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID,
            ]);
            //Assign a user in the task
            $taskUser = factory(TaskUser::class)->create([
                'TAS_UID' => $task->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
            //Create the register in delegation relate to self-service
            $delegation = factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'PRO_ID' => $process->PRO_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_ID' => 0,
                'DEL_DELEGATE_DATE' => date('Y-m-d H:m:s', strtotime("-$i year"))
            ]);
        }

        return [
            'taskUser' => $taskUser,
            'delegation' => $delegation
        ];

    }

    /**
     * This checks the counters is working properly in self-service user assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_user_assigned()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a task self service
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in delegation relate to self-service
        factory(Delegation::class, 25)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $result = $unassigned->getCounter();
        $this->assertEquals(25, $result);
    }

    /**
     * This checks the counters is working properly in self-service-value-based when the variable has a value related
     * with the USR_UID When the value assigned in the variable @@ARRAY_OF_USERS = [USR_UID]
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_value_based_usr_uid()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create a case
        $application = factory(Application::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a task self service value based
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = factory(AppAssignSelfServiceValue::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class)->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $result = $unassigned->getCounter();
        $this->assertEquals(25, $result);
    }

    /**
     * This checks the counters is working properly in self-service and self-service value based
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_mixed_with_self_service_value_based()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create a case
        $application = factory(Application::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a task self service
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in self service
        factory(Delegation::class, 15)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create a task self service value based
        $task1 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = factory(AppAssignSelfServiceValue::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task1->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class)->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self service value based
        factory(Delegation::class, 15)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $result = $unassigned->getCounter();
        $this->assertEquals(30, $result);
    }

    /**
     * This checks the counters is working properly in self-service group assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_group_assigned()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create group
        $group = factory(Groupwf::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Assign a user in the group
        factory(GroupUser::class)->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        //Create a task self service
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the register in self-service
        factory(Delegation::class, 25)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $result = $unassigned->getCounter();
        $this->assertEquals(25, $result);
    }

    /**
     * This checks the counters is working properly in self-service-value-based when the variable has a value related
     * with the GRP_UID When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID]
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_value_based_grp_uid()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create a task self service value based
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create a case
        $application = factory(Application::class)->create();
        //Create group
        $group = factory(Groupwf::class)->create();
        //Create user
        $user = factory(User::class)->create([
            'USR_USERNAME' => 'gary',
            'USR_LASTNAME' => 'Gary',
            'USR_FIRSTNAME' => 'Bailey',
        ]);
        //Assign a user in the group
        factory(GroupUser::class)->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = factory(AppAssignSelfServiceValue::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class)->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $group->GRP_UID,
            'ASSIGNEE_ID' => $group->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $result = $unassigned->getCounter();
        $this->assertEquals(25, $result);
    }

    /**
     * This checks the counters is working properly in self-service user and group assigned in parallel task
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_user_and_group_assigned_parallel_task()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create group
        $group = factory(Groupwf::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Assign a user in the group
        factory(GroupUser::class)->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        //Create a task self service
        $task1 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task1
        factory(TaskUser::class)->create([
            'TAS_UID' => $task1->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task2
        factory(TaskUser::class)->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task3 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $task3->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task4 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $task4->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the register in self-service related to the task1
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task1->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task2
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task2->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task3
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task3->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task4
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task4->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $result = $unassigned->getCounter();
        $this->assertEquals(40, $result);
    }

    /**
     * This checks the counters is working properly in self-service-value-based with GRP_UID and USR_UID in parallel
     * task When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID, USR_UID]
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_value_based_usr_uid_and_grp_uid()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create a case
        $application = factory(Application::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a task1 self service value based
        $task1 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = factory(AppAssignSelfServiceValue::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task1->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class)->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        factory(Delegation::class, 10)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task1->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create a task2 self service value based
        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = factory(AppAssignSelfServiceValue::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task2->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class)->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        factory(Delegation::class, 15)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task2->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $result = $unassigned->getCounter();
        $this->assertEquals(25, $result);
    }

    /**
     * This checks to make sure pagination is working properly in self-service-user-assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_return_self_service_user_assigned_paged()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create application
        $application1 = factory(Application::class)->create([
            'APP_STATUS_ID' => 2,
            'APP_NUMBER' => 2001,
        ]);
        $application2 = factory(Application::class)->create([
            'APP_STATUS_ID' => 2,
            'APP_NUMBER' => 2002,
        ]);
        //Create a task self service
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in delegation relate to self-service
        $res = factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in delegation relate to self-service
        factory(Delegation::class, 26)->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);


        // Get first page
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $unassigned->setOrderByColumn('APP_NUMBER');
        $unassigned->setOrderDirection('DESC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        $results = $unassigned->getData();
        $this->assertCount(25, $results);
        // Get second page
        $unassigned->setOffset(25);
        $unassigned->setLimit(25);
        $results = $unassigned->getData();
        $this->assertCount(25, $results);
        // Get third page
        $unassigned->setOffset(50);
        $unassigned->setLimit(25);
        $results = $unassigned->getData();
        $this->assertCount(1, $results);
    }

    /**
     * This checks to make sure pagination is working properly in elf-service-value-based when the variable has a value
     * related with the USR_UID When the value assigned in the variable @@ARRAY_OF_USERS = [USR_UID]
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_return_self_service_value_based_usr_uid_paged()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a task self service
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create application
        $application1 = factory(Application::class)->create([
            'APP_STATUS_ID' => 2,
            'APP_NUMBER' => 2001,
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = factory(AppAssignSelfServiceValue::class)->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class)->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create application
        $application2 = factory(Application::class)->create([
            'APP_STATUS_ID' => 2,
            'APP_NUMBER' => 2002,
        ]);
        $appSelfValue = factory(AppAssignSelfServiceValue::class)->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class)->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);

        //Create the register in delegation relate to self-service
        factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        factory(Delegation::class, 26)->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);

        // Get first page
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $unassigned->setOrderByColumn('APP_NUMBER');
        $unassigned->setOrderDirection('DESC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        $results = $unassigned->getData();
        $this->assertCount(25, $results);
        // Get second page
        $unassigned->setOffset(25);
        $unassigned->setLimit(25);
        $results = $unassigned->getData();
        $this->assertCount(25, $results);
        // Get third page
        $unassigned->setOffset(50);
        $unassigned->setLimit(25);
        $results = $unassigned->getData();
        $this->assertCount(1, $results);
    }

    /**
     * This checks to make sure pagination is working properly in self-service group assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_return_self_service_group_assigned_paged()
    {
        // Create process
        $process = factory(Process::class)->create();
        // Create group
        $group = factory(Groupwf::class)->create();
        // Create user
        $user = factory(User::class)->create();
        //Create application
        $application1 = factory(Application::class)->create([
            'APP_STATUS_ID' => 2,
        ]);
        //Create application
        $application2 = factory(Application::class)->create([
            'APP_STATUS_ID' => 2,
        ]);
        // Assign a user in the group
        factory(GroupUser::class)->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        // Create a task self service
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        // Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        // Create a task self service
        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        // Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        // Create the register in self-service
        factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        factory(Delegation::class, 26)->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task2->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        // Get first page
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $unassigned->setOrderByColumn('APP_NUMBER');
        $unassigned->setOrderDirection('DESC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        $results = $unassigned->getData();
        $this->assertCount(25, $results);
        // Get second page
        $unassigned->setOffset(25);
        $unassigned->setLimit(25);
        $results = $unassigned->getData();
        $this->assertCount(25, $results);
        // Get third page
        $unassigned->setOffset(50);
        $unassigned->setLimit(25);
        $results = $unassigned->getData();
        $this->assertCount(1, $results);
    }

    /**
     * This checks to make sure pagination is working properly in self-service-value-based when the variable has a
     * value related with the GRP_UID When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID]
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_return_self_service_group_value_based_assigned_paged()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create a task self service value based
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create a case
        $application = factory(Application::class)->create([
            'APP_STATUS_ID' => 2,
        ]);
        $application2 = factory(Application::class)->create([
            'APP_STATUS_ID' => 2,
        ]);
        //Create group
        $group = factory(Groupwf::class)->create();
        //Create user
        $user = factory(User::class)->create([
            'USR_USERNAME' => 'gary',
            'USR_LASTNAME' => 'Gary',
            'USR_FIRSTNAME' => 'Bailey',
        ]);
        //Assign a user in the group
        factory(GroupUser::class)->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = factory(AppAssignSelfServiceValue::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class)->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $group->GRP_UID,
            'ASSIGNEE_ID' => $group->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        $appSelfValue = factory(AppAssignSelfServiceValue::class)->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'APP_UID' => $application2->APP_UID,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class)->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $group->GRP_UID,
            'ASSIGNEE_ID' => $group->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        factory(Delegation::class, 26)->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        // Get first page
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $unassigned->setOrderByColumn('APP_NUMBER');
        $unassigned->setOrderDirection('DESC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        $results = $unassigned->getData();
        $this->assertCount(25, $results);
        // Get second page
        $unassigned->setOffset(25);
        $unassigned->setLimit(25);
        $results = $unassigned->getData();
        $this->assertCount(25, $results);
        // Get third page
        $unassigned->setOffset(50);
        $unassigned->setLimit(25);
        $results = $unassigned->getData();
        $this->assertCount(1, $results);
    }

    /**
     * This ensures get data from self-service-user-assigned without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_test_unassigned_by_user_without_filters()
    {
        // Create factories related to the unassigned cases
        $cases = $this->createSelfServiceUser();
        // Create new object
        $unassigned = new Unassigned();
        // Set the user UID
        $unassigned->setUserUid($cases['taskUser']->USR_UID);
        // Set OrderBYColumn value
        $unassigned->setOrderByColumn('APP_NUMBER');
        // Call to getData method
        $res = $unassigned->getData();
        // This assert that the expected numbers of results are returned
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with case title filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::filters()
     * @test
     */
    public function it_filter_by_thread_title()
    {
        // Create factories related to the unassigned cases
        $cases = $this->createSelfServiceUser();
        $usrUid = $cases->last()->USR_UID;
        $usrId = $cases->last()->USR_ID;
        $title = $cases->last()->DEL_TITLE;
        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();
        // Create new Unassigned object
        $unassigned = new Unassigned();
        $unassigned->setUserUid($usrUid);
        $unassigned->setUserId($usrId);
        // Set the title
        $unassigned->setCaseTitle($title);
        // Get the data
        $res = $unassigned->getData();
        // Asserts
        $this->assertNotEmpty($res);
    }
}