<?php
namespace Tests\unit\workflow\src\ProcessMaker\Model;

use ProcessMaker\Model\AppAssignSelfServiceValue;
use ProcessMaker\Model\AppAssignSelfServiceValueGroup;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\GroupUser;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\ListUnassigned;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessCategory;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use ProcessMaker\Model\User;
use Tests\TestCase;

class ListUnassignedTest extends TestCase
{
    /**
     * This is using instead of DatabaseTransactions
     * @todo DatabaseTransactions is having conflicts with propel
     */
    protected function setUp()
    {
    }

    /**
     * This checks the counters is working properly in self-service user assigned
     * @covers ListUnassigned::doCount
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_user_assigned()
    {
        //Create process
        $process = factory(Process::class, 1)->create();
        //Create user
        $user = factory(User::class, 1)->create();
        //Create a task self service
        $task = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task[0]->TAS_UID,
            'USR_UID' => $user[0]->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 15)->create([
            'TAS_ID' => $task[0]->TAS_ID
        ]);
        $timeStart = microtime(true);
        $result = ListUnassigned::doCount($user[0]->USR_UID);
        $timeEnd = microtime(true);
        $this->assertEquals(15, $result);
        $time = $timeEnd - $timeStart;
        error_log('it_should_count_cases_by_user_with_self_service_user_assigned took [15]--->' . $time);
    }

    /**
     * This checks the counters is working properly in self-service-value-based when the variable has a value related with the USR_UID
     * When the value assigned in the variable @@ARRAY_OF_USERS = [USR_UID]
     * @covers ListUnassigned::doCount
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_value_based_usr_uid()
    {
        //Create process
        $process = factory(Process::class, 1)->create();
        //Create a case
        $application = factory(Application::class, 1)->create();
        //Create user
        $user = factory(User::class, 1)->create();
        //Create a task self service value based
        $task = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = factory(AppAssignSelfServiceValue::class, 1)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task[0]->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class, 1)->create([
            'ID' => $appSelfValue[0]->ID,
            'GRP_UID' => $user[0]->USR_UID,
            'ASSIGNEE_ID' => $user[0]->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 10)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue[0]->DEL_INDEX,
            'TAS_ID' => $task[0]->TAS_ID,
        ]);
        $timeStart = microtime(true);
        $result = ListUnassigned::doCount($user[0]->USR_UID);
        $this->assertEquals(10, $result);
        $timeEnd = microtime(true);
        $time = $timeEnd - $timeStart;
        error_log('it_should_count_cases_by_user_with_self_service_value_based_usr_uid took [10]--->' . $time);
    }

    /**
     * This checks the counters is working properly in self-service and self-service value based
     * @covers ListUnassigned::doCount
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_mixed_with_self_service_value_based()
    {
        //Create process
        $process = factory(Process::class, 1)->create();
        //Create a case
        $application = factory(Application::class, 1)->create();
        //Create user
        $user = factory(User::class, 1)->create();
        //Create a task self service
        $task = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task[0]->TAS_UID,
            'USR_UID' => $user[0]->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in self service
        factory(ListUnassigned::class, 15)->create([
            'TAS_ID' => $task[0]->TAS_ID
        ]);
        //Create a task self service value based
        $task1 = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = factory(AppAssignSelfServiceValue::class, 1)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task1[0]->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class, 1)->create([
            'ID' => $appSelfValue[0]->ID,
            'GRP_UID' => $user[0]->USR_UID,
            'ASSIGNEE_ID' => $user[0]->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self service value based
        factory(ListUnassigned::class, 10)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue[0]->DEL_INDEX,
            'TAS_ID' => $task[0]->TAS_ID,
        ]);

        $timeStart = microtime(true);
        $result = ListUnassigned::doCount($user[0]->USR_UID);
        $timeEnd = microtime(true);
        $this->assertEquals(25, $result);
        $time = $timeEnd - $timeStart;
        error_log('it_should_count_cases_by_user_with_self_service_mixed_with_self_service_value_based took [25]--->' . $time);
    }

    /**
     * This checks the counters is working properly in self-service group assigned
     * @covers ListUnassigned::doCount
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_group_assigned()
    {
        //Create process
        $process = factory(Process::class, 1)->create();
        //Create group
        $group = factory(Groupwf::class, 1)->create();
        //Create user
        $user = factory(User::class, 1)->create();
        //Assign a user in the group
        factory(GroupUser::class, 1)->create([
            'GRP_UID' => $group[0]->GRP_UID,
            'GRP_ID' => $group[0]->GRP_ID,
            'USR_UID' => $user[0]->USR_UID
        ]);
        //Create a task self service
        $task = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task[0]->TAS_UID,
            'USR_UID' => $user[0]->USR_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 15)->create([
            'TAS_ID' => $task[0]->TAS_ID
        ]);
        $timeStart = microtime(true);
        $result = ListUnassigned::doCount($user[0]->USR_UID);
        $timeEnd = microtime(true);
        $this->assertEquals(15, $result);
        $time = $timeEnd - $timeStart;
        error_log('it_should_count_cases_by_user_with_self_service_group_assigned took [15]--->' . $time);
    }

    /**
     * This checks the counters is working properly in self-service-value-based when the variable has a value related with the GRP_UID
     * When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID]
     * @covers ListUnassigned::doCount
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_value_based_grp_uid()
    {
        //Create process
        $process = factory(Process::class, 1)->create();
        //Create a task self service value based
        $task = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Create a case
        $application = factory(Application::class, 1)->create();
        //Create group
        $group = factory(Groupwf::class, 1)->create();
        //Create user
        $user = factory(User::class, 1)->create([
            'USR_USERNAME' => 'gary',
            'USR_LASTNAME' => 'Gary',
            'USR_FIRSTNAME' => 'Bailey',
        ]);
        //Assign a user in the group
        factory(GroupUser::class, 1)->create([
            'GRP_UID' => $group[0]->GRP_UID,
            'GRP_ID' => $group[0]->GRP_ID,
            'USR_UID' => $user[0]->USR_UID,
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = factory(AppAssignSelfServiceValue::class, 1)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'APP_UID' => $application[0]->APP_UID,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task[0]->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class, 1)->create([
            'ID' => $appSelfValue[0]->ID,
            'GRP_UID' => $group[0]->GRP_UID,
            'ASSIGNEE_ID' => $group[0]->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 10)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task[0]->TAS_ID,
        ]);
        $timeStart = microtime(true);
        $result = ListUnassigned::doCount($user[0]->USR_UID);
        $this->assertEquals(10, $result);
        $timeEnd = microtime(true);
        $time = $timeEnd - $timeStart;
        error_log('it_should_count_cases_by_user_with_self_service_value_based_grp_uid took [10]--->' . $time);
    }

    /**
     * This checks the counters is working properly in self-service user and group assigned in parallel task
     * @covers ListUnassigned::doCount
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_user_and_group_assigned_parallel_task()
    {
        //Create process
        $process = factory(Process::class, 1)->create();
        //Create group
        $group = factory(Groupwf::class, 1)->create();
        //Create user
        $user = factory(User::class, 1)->create();
        //Assign a user in the group
        factory(GroupUser::class, 1)->create([
            'GRP_UID' => $group[0]->GRP_UID,
            'GRP_ID' => $group[0]->GRP_ID,
            'USR_UID' => $user[0]->USR_UID
        ]);
        //Create a task self service
        $task1 = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task1
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task1[0]->TAS_UID,
            'USR_UID' => $user[0]->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task2 = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task2
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task2[0]->TAS_UID,
            'USR_UID' => $user[0]->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task3 = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task3[0]->TAS_UID,
            'USR_UID' => $group[0]->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task4 = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task4[0]->TAS_UID,
            'USR_UID' => $group[0]->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the register in list unassigned related to the task1
        factory(ListUnassigned::class, 15)->create([
            'TAS_ID' => $task1[0]->TAS_ID
        ]);
        //Create the register in list unassigned related to the task2
        factory(ListUnassigned::class, 15)->create([
            'TAS_ID' => $task2[0]->TAS_ID
        ]);
        //Create the register in list unassigned related to the task3
        factory(ListUnassigned::class, 15)->create([
            'TAS_ID' => $task3[0]->TAS_ID
        ]);
        //Create the register in list unassigned related to the task4
        factory(ListUnassigned::class, 15)->create([
            'TAS_ID' => $task4[0]->TAS_ID
        ]);
        $timeStart = microtime(true);
        $result = ListUnassigned::doCount($user[0]->USR_UID);
        $timeEnd = microtime(true);
        $this->assertEquals(60, $result);
        $time = $timeEnd - $timeStart;
        error_log('it_should_count_cases_by_user_with_self_service_user_and_group_assigned_parallel_task took [60]--->' . $time);
    }

    /**
     * This checks the counters is working properly in self-service-value-based with GRP_UID and USR_UID in parallel task
     * When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID, USR_UID]
     * @covers ListUnassigned::doCount
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_value_based_usr_uid_and_grp_uid()
    {
        //Create process
        $process = factory(Process::class, 1)->create();
        //Create a case
        $application = factory(Application::class, 1)->create();
        //Create user
        $user = factory(User::class, 1)->create();
        //Create a task1 self service value based
        $task1 = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = factory(AppAssignSelfServiceValue::class, 1)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'TAS_ID' => $task1[0]->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class, 1)->create([
            'ID' => $appSelfValue[0]->ID,
            'GRP_UID' => $user[0]->USR_UID,
            'ASSIGNEE_ID' => $user[0]->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 10)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue[0]->DEL_INDEX,
            'TAS_ID' => $task1[0]->TAS_ID,
        ]);
        //Create a task2 self service value based
        $task2 = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = factory(AppAssignSelfServiceValue::class, 1)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'TAS_ID' => $task2[0]->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class, 1)->create([
            'ID' => $appSelfValue[0]->ID,
            'GRP_UID' => $user[0]->USR_UID,
            'ASSIGNEE_ID' => $user[0]->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 10)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue[0]->DEL_INDEX,
            'TAS_ID' => $task2[0]->TAS_ID,
        ]);
        $timeStart = microtime(true);
        $result = ListUnassigned::doCount($user[0]->USR_UID);
        $this->assertEquals(20, $result);
        $timeEnd = microtime(true);
        $time = $timeEnd - $timeStart;
        error_log('it_should_count_cases_by_user_with_self_service_value_based_usr_uid_and_grp_uid took [20]--->' . $time);
    }

    /**
     * This checks to make sure pagination is working properly
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_return_pages_of_data()
    {
        //Create process
        $process = factory(Process::class, 1)->create();
        //Create user
        $user = factory(User::class, 1)->create();
        //Create a task self service
        $task = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task[0]->TAS_UID,
            'USR_UID' => $user[0]->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 51)->create([
            'TAS_ID' => $task[0]->TAS_ID
        ]);
        //Define the filters
        $filters = ['start' => 0, 'limit' => 25];
        //Get data first page
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(25, $result);
        //Get data second page
        $filters = ['start' => 25, 'limit' => 25];
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(25, $result);
        //Get data third page
        $filters = ['start' => 50, 'limit' => 25];
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(1, $result);
    }

    /**
     * This ensures ordering ascending and descending works by case number APP_NUMBER
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_sort_by_case_number()
    {
        //Create process
        $process = factory(Process::class, 1)->create();
        //Create user
        $user = factory(User::class, 1)->create();
        //Create a task self service
        $task = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task[0]->TAS_UID,
            'USR_UID' => $user[0]->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a case
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 3000
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 1)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        //Create a case
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2000
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 1)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        //Define the filters
        $filters = ['sort' => 'APP_NUMBER', 'dir' => 'ASC'];
        //Get data
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Get the minor case number first
        $this->assertEquals(2000, $result[0]['APP_NUMBER']);
        //Get the major case number second
        $this->assertEquals(3000, $result[1]['APP_NUMBER']);
        //Define the filters
        $filters = ['sort' => 'APP_NUMBER', 'dir' => 'DESC'];
        //Get data
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Get the major case number first
        $this->assertEquals(3000, $result[0]['APP_NUMBER']);
        //Get the minor case number second
        $this->assertEquals(2000, $result[1]['APP_NUMBER']);
    }

    /**
     * This ensures ordering ascending and descending works by case number APP_TITLE
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_sort_by_case_title()
    {
        //Create process
        $process = factory(Process::class, 1)->create();
        //Create user
        $user = factory(User::class, 1)->create();
        //Create a task self service
        $task = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task[0]->TAS_UID,
            'USR_UID' => $user[0]->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a case
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 3001
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 1)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'APP_TITLE' => 'Request nro ' . $application[0]->APP_NUMBER,
        ]);
        //Create a case
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2001
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 1)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'APP_TITLE' => 'Request nro ' . $application[0]->APP_NUMBER,
        ]);
        //Define the filters
        $filters = ['sort' => 'APP_TITLE', 'dir' => 'ASC'];
        //Get data
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Get the minor case title first
        $this->assertEquals('Request nro 2001', $result[0]['APP_TITLE']);
        //Get the major case title second
        $this->assertEquals('Request nro 3001', $result[1]['APP_TITLE']);
        //Define the filters
        $filters = ['sort' => 'APP_TITLE', 'dir' => 'DESC'];
        //Get data
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Get the major case title first
        $this->assertEquals('Request nro 3001', $result[0]['APP_TITLE']);
        //Get the minor case title second
        $this->assertEquals('Request nro 2001', $result[1]['APP_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by case number APP_PRO_TITLE
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_sort_by_process()
    {
        //Create user
        $user = factory(User::class, 1)->create();
        //Create process
        $process = factory(Process::class, 1)->create();
        //Create a task self service
        $task = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task[0]->TAS_UID,
            'USR_UID' => $user[0]->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 1)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'APP_PRO_TITLE' => 'Egypt Supplier Payment Proposal',
        ]);

        //Create the register in list unassigned
        factory(ListUnassigned::class, 1)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'APP_PRO_TITLE' => 'Russia Supplier Payment Proposal',
        ]);
        //Define the filters
        $filters = ['sort' => 'APP_PRO_TITLE', 'dir' => 'ASC'];
        //Get data
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Get the minor process name first
        $this->assertEquals('Egypt Supplier Payment Proposal', $result[0]['APP_PRO_TITLE']);
        //Get the major process name second
        $this->assertEquals('Russia Supplier Payment Proposal', $result[1]['APP_PRO_TITLE']);
        //Define the filters
        $filters = ['sort' => 'APP_PRO_TITLE', 'dir' => 'DESC'];
        //Get data
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Get the major process name first
        $this->assertEquals('Russia Supplier Payment Proposal', $result[0]['APP_PRO_TITLE']);
        //Get the minor process name second
        $this->assertEquals('Egypt Supplier Payment Proposal', $result[1]['APP_PRO_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by case number APP_TAS_TITLE
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_sort_by_task()
    {
        //Create user
        $user = factory(User::class, 1)->create();
        //Create process
        $process = factory(Process::class, 1)->create();
        //Create a task self service
        $task = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task[0]->TAS_UID,
            'USR_UID' => $user[0]->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 1)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'APP_TAS_TITLE' => 'Initiate Request',
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 1)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'APP_TAS_TITLE' => 'Waiting for AP Manager Validation',
        ]);
        //Define the filters
        $filters = ['sort' => 'APP_TAS_TITLE', 'dir' => 'ASC'];
        //Get data
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Get the minor task name first
        $this->assertEquals('Initiate Request', $result[0]['APP_TAS_TITLE']);
        //Get the major task name second
        $this->assertEquals('Waiting for AP Manager Validation', $result[1]['APP_TAS_TITLE']);
        //Define the filters
        $filters = ['sort' => 'APP_TAS_TITLE', 'dir' => 'DESC'];
        //Get data
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Get the major task name first
        $this->assertEquals('Waiting for AP Manager Validation', $result[0]['APP_TAS_TITLE']);
        //Get the minor task namesecond
        $this->assertEquals('Initiate Request', $result[1]['APP_TAS_TITLE']);
    }

    /**
     * This checks to make sure filter by category is working properly
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_return_data_filtered_by_process_category()
    {
        //Create user
        $user = factory(User::class, 1)->create();
        //Create a category
        $category = factory(ProcessCategory::class, 1)->create();
        //Create process
        $process = factory(Process::class, 1)->create([
            'PRO_CATEGORY' => $category[0]->CATEGORY_UID
        ]);
        //Create a category
        $category1 = factory(ProcessCategory::class, 1)->create();
        //Create process
        $process1 = factory(Process::class, 1)->create([
            'PRO_CATEGORY' => $category1[0]->CATEGORY_UID
        ]);
        //Create a task self service
        $task = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task[0]->TAS_UID,
            'USR_UID' => $user[0]->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 2)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'PRO_UID' => $process[0]->PRO_UID,
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 5)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'PRO_UID' => $process1[0]->PRO_UID,
        ]);
        //Get all data
        $result = ListUnassigned::loadList($user[0]->USR_UID);
        $this->assertCount(7, $result);
        //Define the filters
        $filters = ['category' => $category[0]->CATEGORY_UID];
        //Get data
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Get the minor case number first
        $this->assertEquals($category[0]->CATEGORY_UID, $result[0]['PRO_CATEGORY']);
        //Get the major case number second
        $this->assertEquals($category[0]->CATEGORY_UID, $result[1]['PRO_CATEGORY']);
    }

    /**
     * This checks to make sure filter by category is working properly
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_return_data_filtered_by_generic_search()
    {
        //Create user
        $user = factory(User::class, 1)->create();
        //Create process
        $process = factory(Process::class, 1)->create();
        //Create a task self service
        $task = factory(Task::class, 1)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process[0]->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class, 1)->create([
            'TAS_UID' => $task[0]->TAS_UID,
            'USR_UID' => $user[0]->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 2)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'APP_TITLE' => 'This is a case name',
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 2)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'APP_PRO_TITLE' => 'This is a process name',
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 2)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'APP_TAS_TITLE' => 'This is a task name',
        ]);
        //Create other registers
        factory(ListUnassigned::class, 4)->create([
            'TAS_ID' => $task[0]->TAS_ID
        ]);
        //Define the filters
        $filters = ['search' => 'case name'];
        //Get data related to the search
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Define the filters
        $filters = ['search' => 'process name'];
        //Get data related to the search
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Define the filters
        $filters = ['search' => 'task name'];
        //Get data related to the search
        $result = ListUnassigned::loadList($user[0]->USR_UID, $filters);
        $this->assertCount(2, $result);
    }
}

