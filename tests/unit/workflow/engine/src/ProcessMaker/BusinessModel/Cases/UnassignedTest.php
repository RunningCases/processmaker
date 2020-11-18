<?php

namespace Tests\unit\workflow\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
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
        $unassigned->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
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
        $unassigned->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
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
        $unassigned->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
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
        $unassigned->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
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
     * This ensures ordering ascending and descending works by case number APP_NUMBER in self-service-user-assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_return_self_service_user_assigned_sort_by_case_number()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create application
        $application1 = factory(Application::class)->create([
            'APP_STATUS_ID' => 2,
            'APP_NUMBER' => 2001,
        ]);
        $application2 = factory(Application::class)->create([
            'APP_STATUS_ID' => 2,
            'APP_NUMBER' => 2002,
        ]);
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
        factory(Delegation::class, 2)->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in delegation relate to self-service
        factory(Delegation::class, 2)->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        // Get first page, the minor case id
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $unassigned->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        $unassigned->setOrderDirection('ASC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        $results = $unassigned->getData();
        $this->assertEquals(2001, $results[0]['APP_NUMBER']);
        $this->assertEquals(2001, $results[1]['APP_NUMBER']);
        $this->assertEquals(2002, $results[2]['APP_NUMBER']);
        $this->assertEquals(2002, $results[3]['APP_NUMBER']);
        // Get first page, the major case id
        $unassigned->setOrderDirection('DESC');
        $results = $unassigned->getData();
        $this->assertEquals(2002, $results[0]['APP_NUMBER']);
        $this->assertEquals(2002, $results[1]['APP_NUMBER']);
        $this->assertEquals(2001, $results[2]['APP_NUMBER']);
        $this->assertEquals(2001, $results[3]['APP_NUMBER']);
    }

    /**
     * This ensures ordering ascending and descending works by case title APP_TITLE in self-service-user-assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_return_self_service_user_assigned_sort_by_case_title()
    {
        $this->markTestIncomplete(
            'This test needs to write when the column DELEGATION.DEL_THREAD was added'
        );
        //Create process
        $process = factory(Process::class)->create();
        //Create application
        $application1 = factory(Application::class)->create([
            'APP_STATUS_ID' => 2,
            'APP_NUMBER' => 2001,
            'APP_TITLE' => 'Request # 2001'
        ]);
        $application2 = factory(Application::class)->create([
            'APP_STATUS_ID' => 2,
            'APP_NUMBER' => 2002,
            'APP_TITLE' => 'Request # 2002'
        ]);
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
        factory(Delegation::class, 2)->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in delegation relate to self-service
        factory(Delegation::class, 2)->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $unassigned->setOrderByColumn('APPLICATION.APP_TITLE');
        $unassigned->setOrderDirection('ASC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        // Get first page, the minor case title
        $results = $unassigned->getData();
        $this->assertEquals('Request # 2001', $results[0]['APP_TITLE']);
        $this->assertEquals('Request # 2001', $results[1]['APP_TITLE']);
        $this->assertEquals('Request # 2002', $results[2]['APP_TITLE']);
        $this->assertEquals('Request # 2002', $results[3]['APP_TITLE']);
        // Get first page, the major case title
        $unassigned->setOrderDirection('DESC');
        $results = $unassigned->getData();
        $this->assertEquals('Request # 2002', $results[0]['APP_TITLE']);
        $this->assertEquals('Request # 2002', $results[1]['APP_TITLE']);
        $this->assertEquals('Request # 2001', $results[2]['APP_TITLE']);
        $this->assertEquals('Request # 2001', $results[3]['APP_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by case title PRO_TITLE in self-service-user-assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_return_self_service_user_assigned_sort_by_process()
    {
        //Create user
        $user = factory(User::class)->create();
        for ($i = 1; $i <= 2; $i++) {
            $process = factory(Process::class)->create();
            $application = factory(Application::class)->create([
                'APP_STATUS_ID' => 2
            ]);
            $task = factory(Task::class)->create([
                'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
                'TAS_GROUP_VARIABLE' => '',
                'PRO_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID
            ]);
            //Assign a user in the task
            factory(TaskUser::class)->create([
                'TAS_UID' => $task->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
            //Create the register in delegation relate to self-service
            factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'PRO_ID' => $process->PRO_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_ID' => 0,
            ]);
        }
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $unassigned->setOrderByColumn('PRO_TITLE');
        $unassigned->setOrderDirection('ASC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        // Get first page, the minor process title
        $results = $unassigned->getData();
        $this->assertGreaterThan($results[0]['PRO_TITLE'], $results[1]['PRO_TITLE']);
        // Get first page, the major process title
        $unassigned->setOrderDirection('DESC');
        $results = $unassigned->getData();
        $this->assertLessThan($results[0]['PRO_TITLE'], $results[1]['PRO_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by task title TAS_TITLE in self-service-user-assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_return_self_service_user_assigned_sort_by_task_title()
    {
        //Create user
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
            ]);
            //Assign a user in the task
            factory(TaskUser::class)->create([
                'TAS_UID' => $task->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
            factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'PRO_ID' => $process->id,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_ID' => 0,
            ]);
        }
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $unassigned->setOrderByColumn('TAS_TITLE');
        $unassigned->setOrderDirection('ASC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        // Get first page, the minor task title
        $results = $unassigned->getData();
        $this->assertGreaterThan($results[0]['TAS_TITLE'], $results[1]['TAS_TITLE']);
        // Get first page, the major task title
        $unassigned->setOrderDirection('DESC');
        $results = $unassigned->getData();
        $this->assertLessThan($results[0]['TAS_TITLE'], $results[1]['TAS_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by due date DEL_TASK_DUE_DATE in self-service-user-assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_return_self_service_user_assigned_sort_due_date()
    {
        //Create user
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
            ]);
            //Assign a user in the task
            factory(TaskUser::class)->create([
                'TAS_UID' => $task->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
            //Create the register in delegation relate to self-service
            factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'PRO_ID' => $process->PRO_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_ID' => 0,
            ]);
        }
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $unassigned->setOrderByColumn('DEL_TASK_DUE_DATE');
        $unassigned->setOrderDirection('ASC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        // Get first page, the minor due date
        $results = $unassigned->getData();
        $this->assertGreaterThan($results[0]['DEL_TASK_DUE_DATE'], $results[1]['DEL_TASK_DUE_DATE']);
        // Get first page, the major due date
        $unassigned->setOrderDirection('DESC');
        $results = $unassigned->getData();
        $this->assertLessThan($results[0]['DEL_TASK_DUE_DATE'], $results[1]['DEL_TASK_DUE_DATE']);
    }

    /**
     * This ensures ordering ascending and descending works by last modified APP_UPDATE_DATE in
     * self-service-user-assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_return_self_service_user_assigned_sort_delegate_date()
    {
        //Create user
        $user = factory(User::class)->create();
        for ($i = 1; $i <= 2; $i++) {
            //Create process
            $process = factory(Process::class)->create();
            //Create application
            $application = factory(Application::class)->create([
                'APP_STATUS_ID' => 2,
            ]);
            //Create a task self service
            $task = factory(Task::class)->create([
                'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
                'TAS_GROUP_VARIABLE' => '',
                'PRO_UID' => $process->PRO_UID,
            ]);
            //Assign a user in the task
            factory(TaskUser::class)->create([
                'TAS_UID' => $task->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
            //Create the register in delegation relate to self-service
            factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'PRO_ID' => $process->PRO_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_ID' => 0,
            ]);
        }
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $unassigned->setOrderByColumn('DEL_DELEGATE_DATE');
        $unassigned->setOrderDirection('ASC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        // Get first page, the minor update date
        $results = $unassigned->getData();
        $this->assertGreaterThan($results[0]['DEL_DELEGATE_DATE'], $results[1]['DEL_DELEGATE_DATE']);

        // Get first page, the major update date
        $unassigned->setOrderDirection('DESC');
        $results = $unassigned->getData();
        $this->assertLessThan($results[0]['DEL_DELEGATE_DATE'], $results[1]['DEL_DELEGATE_DATE']);
    }

    /**
     * This ensures searching by newest than and review the page in self-service-user-assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_search_self_service_user_assigned_by_newest_than()
    {
        //Create user
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
            factory(TaskUser::class)->create([
                'TAS_UID' => $task->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
            //Create the register in delegation relate to self-service
            $del = factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'PRO_ID' => $process->PRO_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_ID' => 0,
                'DEL_DELEGATE_DATE' => date('Y-m-d H:m:s', strtotime("+$i year"))
            ]);
        }
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $dateToFilter = date('Y-m-d', strtotime('+1 year'));
        $unassigned->setNewestThan($dateToFilter);
        $unassigned->setOrderByColumn('DEL_DELEGATE_DATE');
        $unassigned->setOrderDirection('ASC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        // Get the newest than (>=) delegate date
        $results = $unassigned->getData();
        $this->assertGreaterThan($results[0]['DEL_DELEGATE_DATE'], $results[1]['DEL_DELEGATE_DATE']);
        // Get the newest than (>=) delegate date
        $unassigned->setNewestThan($dateToFilter);
        $unassigned->setOrderDirection('DESC');
        $results = $unassigned->getData();
        $this->assertLessThan($results[0]['DEL_DELEGATE_DATE'], $results[1]['DEL_DELEGATE_DATE']);
    }

    /**
     * This ensures searching by newest than and review the page in self-service-user-assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_search_self_service_user_assigned_by_oldest_than()
    {
        //Create user
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
            ]);
            //Assign a user in the task
            factory(TaskUser::class)->create([
                'TAS_UID' => $task->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
            //Create the register in delegation relate to self-service
            $del = factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'PRO_ID' => $process->PRO_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_ID' => 0,
                'DEL_DELEGATE_DATE' => date('Y-m-d H:m:s', strtotime("-$i year"))
            ]);
        }
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $dateToFilter = date('Y-m-d', strtotime('+1 year'));
        $unassigned->setOldestThan($dateToFilter);
        $unassigned->setOrderByColumn('DEL_DELEGATE_DATE');
        $unassigned->setOrderDirection('ASC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        // Get the oldest than (<=) delegate date
        $results = $unassigned->getData();
        $this->assertGreaterThan($results[0]['DEL_DELEGATE_DATE'], $results[1]['DEL_DELEGATE_DATE']);
    }

    /**
     * This ensures searching specific cases and review the page in self-service-user-assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_search_self_service_user_assigned_specific_case_uid()
    {
        //Create user
        $user = factory(User::class)->create();
        for ($i = 1; $i <= 2; $i++) {
            //Create process
            $process = factory(Process::class)->create([
                'PRO_TITLE' => 'China Supplier Payment Proposal'
            ]);
            //Create application
            $application = factory(Application::class)->create([
                'APP_STATUS_ID' => 2
            ]);
            //Create a task self service
            $task = factory(Task::class)->create([
                'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
                'TAS_GROUP_VARIABLE' => '',
                'PRO_UID' => $process->PRO_UID,
            ]);
            //Assign a user in the task
            factory(TaskUser::class)->create([
                'TAS_UID' => $task->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
            //Create the register in delegation relate to self-service
            factory(Delegation::class)->create([
                'APP_UID' => $application->APP_UID,
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'PRO_ID' => $process->PRO_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_ID' => 0
            ]);
        }
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $unassigned->setOrderByColumn('APP_DELEGATION.APP_UID');
        $unassigned->setOrderDirection('ASC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        // Get the specific case uid
        $unassigned->setCaseUid($application->APP_UID);
        $results = $unassigned->getData();
        $this->assertEquals($application->APP_UID, $results[0]['APP_UID']);
    }

    /**
     * This ensures searching specific cases and review the page in self-service-user-assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_search_self_service_user_assigned_specific_cases_uid_array()
    {
        //Create user
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
            ]);
            //Assign a user in the task
            factory(TaskUser::class)->create([
                'TAS_UID' => $task->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
            //Create the register in delegation relate to self-service
            factory(Delegation::class)->create([
                'APP_UID' => $application->APP_UID,
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'PRO_ID' => $process->PRO_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_ID' => 0
            ]);
        }
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $unassigned->setCasesUids([$application->APP_UID]);
        $unassigned->setOrderByColumn('APP_DELEGATION.APP_UID');
        $unassigned->setOrderDirection('ASC');
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        // Get the specific cases uid's
        $results = $unassigned->getData();
        $this->assertCount(1, $results);
        // Get the specific cases uid's
        $unassigned->setCasesUids([$application->APP_UID]);
        $results = $unassigned->getData();
        $this->assertEquals($application->APP_UID, $results[0]['APP_UID']);
        // Get the specific cases uid's
        $unassigned->setCasesUids([$application->APP_UID]);
        $results = $unassigned->getData();
        $this->assertEquals($application->APP_UID, $results[0]['APP_UID']);
    }

    /**
     * This ensures searching specific process and review the page in self-service-user-assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @test
     */
    public function it_should_search_self_service_user_assigned_specific_process()
    {
        //Create user
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
            factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'PRO_ID' => $process->PRO_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_ID' => 0,
            ]);
        }
        $unassigned = new Unassigned;
        $unassigned->setUserUid($user->USR_UID);
        $unassigned->setOrderByColumn('PRO_TITLE');
        $unassigned->setOrderDirection('ASC');
        $unassigned->setProcessId($process->PRO_ID);
        $unassigned->setOffset(0);
        $unassigned->setLimit(25);
        // Get first page, the minor process title
        $results = $unassigned->getData();
        $this->assertEquals($process->PRO_TITLE, $results[0]['PRO_TITLE']);
    }
}