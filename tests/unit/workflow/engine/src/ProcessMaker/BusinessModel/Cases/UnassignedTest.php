<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use DateInterval;
use Datetime;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Unassigned;
use ProcessMaker\Model\AdditionalTables;
use ProcessMaker\Model\AppAssignSelfServiceValue;
use ProcessMaker\Model\AppAssignSelfServiceValueGroup;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\CaseList;
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
     * Method set up.
     */
    public function setUp()
    {
        parent::setUp();
        Delegation::truncate();
        Groupwf::truncate();
    }

    /**
     * Create unassigned cases factories
     *
     * @param int $relation, [1 = user assigned, 2 = group assigned]
     *
     * @return array
     */
    public function createSelfServiceUserOrGroup($relation = 1)
    {
        // Create user`
        $user = factory(User::class)->create();
        // Create a group
        $group = factory(Groupwf::class)->create();
        // Assign a user in the group
        factory(GroupUser::class)->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        // Create self-services
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
                'TU_RELATION' => $relation, //Related to the user
                'TU_TYPE' => 1
            ]);
            //Create the register in delegation relate to self-service
            $delegation = factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'PRO_ID' => $process->PRO_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_ID' => 0,
                'DEL_DELEGATE_DATE' => date('Y-m-d H:i:s', strtotime("-$i year"))
            ]);
        }

        return [
            'taskUser' => $taskUser,
            'delegation' => $delegation
        ];
    }

    /**
     * Create unassigned cases factories
     *
     * @param int $relation, [1 = user assigned, 2 = group assigned]
     * @param bool $userAssignee
     *
     * @return array
     */
    public function createSelfServiceByVariable($relation = 1, $userAssignee = true)
    {
        // Create user`
        $user = factory(User::class)->create();
        // Create a group
        $group = factory(Groupwf::class)->create();
        // Assign a user in the group
        factory(GroupUser::class)->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        // Create self-services
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
                'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
                'PRO_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID,
            ]);
            //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
            $appSelfValueUser = factory(AppAssignSelfServiceValue::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'DEL_INDEX' => 2,
                'TAS_ID' => $task->TAS_ID
            ]);
            $selfValueGroup = factory(AppAssignSelfServiceValueGroup::class)->create([
                'ID' => $appSelfValueUser->ID,
                'GRP_UID' => $user->USR_UID,
                'ASSIGNEE_ID' => ($userAssignee) ? $user->USR_ID : $group->GRP_ID,
                'ASSIGNEE_TYPE' => $relation
            ]);
            //Create the register in delegation relate to self-service
            $delegation = factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'DEL_INDEX' => $appSelfValueUser->DEL_INDEX,
                'TAS_ID' => $task->TAS_ID,
                'PRO_ID' => $process->PRO_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_ID' => 0,
                'DEL_DELEGATE_DATE' => date('Y-m-d H:i:s', strtotime("-$i year"))
            ]);
        }

        return [
            'selfValue' => $selfValueGroup,
            'user' => $user,
            'delegation' => $delegation
        ];
    }

    /**
     * Create many unassigned cases for one user
     * 
     * @param int
     * @return object
     */
    public function createMultipleUnassigned($cases)
    {
        $user = factory(\ProcessMaker\Model\User::class)->create();

        for ($i = 0; $i < $cases; $i = $i + 1) {
            $process = factory(Process::class)->create();
            $application = factory(Application::class)->create([
                'APP_STATUS_ID' => 2
            ]);
            $task = factory(Task::class)->create([
                'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
                'TAS_GROUP_VARIABLE' => '',
                'PRO_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID,
            ]);
            factory(TaskUser::class)->create([
                'TAS_UID' => $task->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
            factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'PRO_ID' => $process->PRO_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_ID' => 0,
                'DEL_DELEGATE_DATE' => date('Y-m-d H:i:s', strtotime("-$i year"))
            ]);
        }
        return $user;
    }

    /**
     * This checks the counters is working properly in self-service user assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_count_selfService_userAssigned()
    {
        // Create factories related to the unassigned cases
        $cases = $this->createSelfServiceUserOrGroup();
        //Review the count self-service
        $unassigned = new Unassigned;
        // Apply filters
        $unassigned->setUserUid($cases['taskUser']->USR_UID);
        $unassigned->setUserId($cases['delegation']->USR_ID);
        $result = $unassigned->getCounter();
        $this->assertNotEmpty($result);
    }

    /**
     * This checks the counters is working properly in self-service-value-based when the variable has a value related
     * with the USR_UID When the value assigned in the variable @@ARRAY_OF_USERS = [USR_UID]
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_count_selfService_valueBased_usrUid()
    {
        $cases = $this->createSelfServiceByVariable();
        //Review the count self-service
        $unassigned = new Unassigned;
        // Apply filters
        $unassigned->setUserUid($cases['user']->USR_UID);
        $unassigned->setUserId($cases['delegation']->USR_ID);
        $result = $unassigned->getCounter();
        $this->assertNotEmpty($result);
    }

    /**
     * This checks the counters is working properly in self-service group assigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_count_selfService_groupAssigned()
    {
        // Create factories related to the unassigned cases
        $cases = $this->createSelfServiceUserOrGroup(2);
        //Review the count self-service
        $unassigned = new Unassigned;
        // Apply filters
        $unassigned->setUserUid($cases['taskUser']->USR_UID);
        $unassigned->setUserId($cases['delegation']->USR_ID);
        $result = $unassigned->getCounter();
        $this->assertNotEmpty($result);
    }

    /**
     * This checks the counters is working properly in self-service-value-based when the variable has a value related
     * with the GRP_UID When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID]
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_count_selfService_valueBased_groupUid()
    {
        $cases = $this->createSelfServiceByVariable(2, false);
        //Review the count self-service
        $unassigned = new Unassigned;
        // Apply filters
        $unassigned->setUserUid($cases['user']->USR_UID);
        $unassigned->setUserId($cases['delegation']->USR_ID);
        $result = $unassigned->getCounter();
        $this->assertNotEmpty($result);
    }

    /**
     * This checks the counters is working properly in self-service user and group assigned in parallel task
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_count_self_service_mixed_parallel()
    {
        // Create factories related to the unassigned cases
        $casesUser = $this->createSelfServiceUserOrGroup();
        $casesGroup = $this->createSelfServiceUserOrGroup(2);
        //Review the count self-service
        $unassigned = new Unassigned;
        // Apply filters
        $unassigned->setUserUid($casesUser['taskUser']->USR_UID);
        $unassigned->setUserId($casesUser['delegation']->USR_ID);
        $result = $unassigned->getCounter();
        $this->assertNotEmpty($result);
        // Apply filters
        $unassigned->setUserUid($casesGroup['taskUser']->USR_UID);
        $unassigned->setUserId($casesGroup['delegation']->USR_ID);
        $result = $unassigned->getCounter();
        $this->assertNotEmpty($result);
    }

    /**
     * This checks the counters is working properly in self-service-value-based with GRP_UID and USR_UID in parallel
     * task When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID, USR_UID]
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_count_selfService_valueBased_groupUid_usrUid()
    {
        $casesUser = $this->createSelfServiceByVariable();
        $casesGroup = $this->createSelfServiceByVariable(2, false);
        // Review the count self-service
        $unassigned = new Unassigned;
        // Apply filters
        $unassigned->setUserUid($casesUser['user']->USR_UID);
        $unassigned->setUserId($casesUser['delegation']->USR_ID);
        $result = $unassigned->getCounter();
        $this->assertNotEmpty($result);
        $unassigned = new Unassigned;
        // Apply filters
        $unassigned->setUserUid($casesGroup['user']->USR_UID);
        $unassigned->setUserId($casesGroup['delegation']->USR_ID);
        $result = $unassigned->getCounter();
        $this->assertNotEmpty($result);
    }

    /**
     * This ensures get data from self-service-user-assigned without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @covers \ProcessMaker\Model\Delegation::scopeSelfService()
     * @test
     */
    public function it_test_unassigned_by_user_without_filters()
    {
        // Create factories related to the unassigned cases
        $cases = $this->createSelfServiceUserOrGroup();
        // Create new object
        $unassigned = new Unassigned();
        // Apply filters
        $unassigned->setUserUid($cases['taskUser']->USR_UID);
        $unassigned->setUserId($cases['delegation']->USR_ID);
        // Set OrderByColumn value
        $unassigned->setOrderByColumn('APP_NUMBER');
        // Call to getData method
        $res = $unassigned->getData();
        // This assert that the expected numbers of results are returned
        $this->assertNotEmpty($res);
    }

    /**
     * This ensures get data from self-service-user-assigned with filter setCasesNumbers
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::filters()
     * @test
     */
    public function it_filter_by_case_numbers()
    {
        // Create factories related to the unassigned cases
        $cases = $this->createSelfServiceUserOrGroup();
        // Create new object
        $unassigned = new Unassigned();
        // Apply filters
        $unassigned->setUserUid($cases['taskUser']->USR_UID);
        $unassigned->setUserId($cases['delegation']->USR_ID);
        $unassigned->setCasesNumbers([$cases['delegation']->APP_NUMBER]);
        // Set OrderBYColumn value
        $unassigned->setOrderByColumn('APP_NUMBER');
        // Call to getData method
        $res = $unassigned->getData();
        // This assert that the expected numbers of results are returned
        $this->assertNotEmpty($res);
    }

    /**
     * This ensures get data from self-service-user-assigned with filter setRangeCasesFromTo
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::filters()
     * @test
     */
    public function it_filter_by_range_cases()
    {
        // Create factories related to the unassigned cases
        $cases = $this->createSelfServiceUserOrGroup();
        // Create new object
        $unassigned = new Unassigned();
        // Apply filters
        $unassigned->setUserUid($cases['taskUser']->USR_UID);
        $unassigned->setUserId($cases['delegation']->USR_ID);
        $rangeOfCases = $cases['delegation']->APP_NUMBER . "-" . $cases['delegation']->APP_NUMBER;
        $unassigned->setRangeCasesFromTo([$rangeOfCases]);
        // Call to getData method
        $res = $unassigned->getData();
        // This assert that the expected numbers of results are returned
        $this->assertNotEmpty($res);
    }

    /**
     * This ensures get data from self-service-user-assigned with setDelegateFrom and setDelegateTo filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::filters()
     * @test
     */
    public function it_filter_by_delegate_from_to()
    {
        // Create factories related to the unassigned cases
        $cases = $this->createSelfServiceUserOrGroup();
        // Create new object
        $unassigned = new Unassigned();
        // Apply filters
        $unassigned->setUserUid($cases['taskUser']->USR_UID);
        $unassigned->setUserId($cases['delegation']->USR_ID);
        $unassigned->setDelegateFrom(date('Y-m-d'));
        $unassigned->setDelegateTo(date('Y-m-d'));
        // Call to getData method
        $res = $unassigned->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEmpty($res);
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
        $cases = $this->createSelfServiceUserOrGroup();
        $usrUid = $cases['taskUser']->USR_UID;
        $usrId = $cases['delegation']->USR_ID;
        $title = $cases['delegation']->DEL_TITLE;
        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();
        // Create new Unassigned object
        $unassigned = new Unassigned();
        // Apply filters
        $unassigned->setUserUid($usrUid);
        $unassigned->setUserId($usrId);
        $unassigned->setCaseTitle($title);
        // Get the data
        $res = $unassigned->getData();
        // Asserts
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getCounter method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounter()
     * @test
     */
    public function it_get_counter()
    {
        // Create factories related to the unassigned cases
        $cases = $this->createMultipleUnassigned(3);
        $unassigned = new Unassigned();
        $unassigned->setUserId($cases->USR_ID);
        $unassigned->setUserUid($cases->USR_UID);
        // Get the total for the pagination
        $res = $unassigned->getCounter();
        $this->assertEquals(3, $res);
    }

    /**
     * It tests the getPagingCounters() method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getPagingCounters()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::filters()
     * @test
     */
    public function it_should_test_get_paging_counters_method()
    {
        // Create factories related to the unassigned cases
        $cases = $this->createMultipleUnassigned(3);
        $unassigned = new Unassigned();
        $unassigned->setUserId($cases->USR_ID);
        $unassigned->setUserUid($cases->USR_UID);
        // Get the total for the pagination
        $res = $unassigned->getPagingCounters();
        $this->assertEquals(3, $res);

        $cases = $this->createSelfServiceUserOrGroup();
        // Create new object
        $unassigned = new Unassigned();
        // Set the user
        $unassigned->setUserUid($cases['taskUser']->USR_UID);
        $unassigned->setUserId($cases['delegation']->USR_ID);
        // Apply some filters
        $unassigned->setCaseNumber($cases['delegation']->APP_NUMBER);
        $unassigned->setProcessId($cases['delegation']->PRO_ID);
        $unassigned->setTaskId($cases['delegation']->TAS_ID);
        // Get the total for the pagination with some filters
        $res = $unassigned->getPagingCounters();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getCountersByProcesses() method without filters
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_no_filter()
    {
        $cases = $this->createMultipleUnassigned(3);
        $unassigned = new Unassigned();
        $unassigned->setUserId($cases->USR_ID);
        $unassigned->setUserUid($cases->USR_UID);
        $res = $unassigned->getCountersByProcesses();
        $this->assertCount(3, $res);
    }

    /**
     * It tests the getCountersByProcesses() method with the category filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_category()
    {
        $user = factory(User::class)->create();
        $process1 = factory(Process::class)->create([
            'CATEGORY_ID' => 2
        ]);
        $process2 = factory(Process::class)->create([
            'CATEGORY_ID' => 3
        ]);
        $application = factory(Application::class)->create([
            'APP_STATUS_ID' => 2
        ]);
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'PRO_ID' => $process1->PRO_ID,
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'PRO_ID' => $process1->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => date('Y-m-d H:i:s', strtotime("-1 year"))
        ]);
        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process2->PRO_UID,
            'PRO_ID' => $process2->PRO_ID,
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task2->TAS_ID,
            'PRO_ID' => $process2->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => date('Y-m-d H:i:s', strtotime("-2 year"))
        ]);
        $unassigned = new Unassigned();
        $unassigned->setUserId($user->USR_ID);
        $unassigned->setUserUid($user->USR_UID);
        $res = $unassigned->getCountersByProcesses(2);
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCountersByProcesses() method with the top ten filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_top_ten()
    {
        $cases = $this->createMultipleUnassigned(20);
        $unassigned = new Unassigned();
        $unassigned->setUserId($cases->USR_ID);
        $unassigned->setUserUid($cases->USR_UID);
        $res = $unassigned->getCountersByProcesses(null, true);
        $this->assertCount(10, $res);
    }

    /**
     * It tests the getCountersByProcesses() method with the processes filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCountersByProcesses()
     * @test
     */
    public function it_should_test_get_counters_by_processes_method_processes()
    {
        $user = factory(User::class)->create();
        $process1 = factory(Process::class)->create([
            'CATEGORY_ID' => 2
        ]);
        $process2 = factory(Process::class)->create([
            'CATEGORY_ID' => 3
        ]);
        $application = factory(Application::class)->create([
            'APP_STATUS_ID' => 2
        ]);
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'PRO_ID' => $process1->PRO_ID,
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'PRO_ID' => $process1->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => date('Y-m-d H:i:s', strtotime("-1 year"))
        ]);
        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process2->PRO_UID,
            'PRO_ID' => $process2->PRO_ID,
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task2->TAS_ID,
            'PRO_ID' => $process2->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => date('Y-m-d H:i:s', strtotime("-2 year"))
        ]);
        $unassigned = new Unassigned();
        $unassigned->setUserId($user->USR_ID);
        $unassigned->setUserUid($user->USR_UID);
        $res = $unassigned->getCountersByProcesses(null, false, [$process1->PRO_ID]);
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCountersByRange() method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCountersByRange()
     * @test
     */
    public function it_should_test_get_counters_by_range_method()
    {
        $user = factory(User::class)->create();
        $process1 = factory(Process::class)->create([
            'CATEGORY_ID' => 2
        ]);
        $process2 = factory(Process::class)->create([
            'CATEGORY_ID' => 3
        ]);
        $application = factory(Application::class)->create([
            'APP_STATUS_ID' => 2
        ]);
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'PRO_ID' => $process1->PRO_ID,
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'PRO_ID' => $process1->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => '2021-05-21 09:52:32'
        ]);
        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process2->PRO_UID,
            'PRO_ID' => $process2->PRO_ID,
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task2->TAS_ID,
            'PRO_ID' => $process2->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => '2021-05-24 09:52:32'
        ]);
        $unassigned = new Unassigned();
        $unassigned->setUserId($user->USR_ID);
        $unassigned->setUserUid($user->USR_UID);

        $res = $unassigned->getCountersByRange();
        $this->assertCount(2, $res);

        $res = $unassigned->getCountersByRange(null, null, null, 'month');
        $this->assertCount(1, $res);

        $res = $unassigned->getCountersByRange(null, null, null, 'year');
        $this->assertCount(1, $res);

        $res = $unassigned->getCountersByRange($process1->PRO_ID);
        $this->assertCount(1, $res);

        $res = $unassigned->getCountersByRange(null, '2021-05-20', '2021-05-23');
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCustomListCount() method
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCustomListCount()
     * @test
     */
    public function it_should_test_getCustomListCount_method()
    {
        $cases = $this->createMultipleUnassigned(0);

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
            'CAL_TYPE' => 'unassigned',
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'USR_ID' => $cases->USR_ID
        ]);

        $unassigned = new Unassigned();
        $unassigned->setUserId($cases->USR_ID);
        $unassigned->setUserUid($cases->USR_UID);

        $res = $unassigned->getCustomListCount($caseList->CAL_ID, 'unassigned');

        //assertions
        $this->assertArrayHasKey('label', $res);
        $this->assertArrayHasKey('name', $res);
        $this->assertArrayHasKey('description', $res);
        $this->assertArrayHasKey('tableName', $res);
        $this->assertArrayHasKey('total', $res);

        $this->assertEquals($additionalTables->ADD_TAB_NAME, $res['tableName']);
        $this->assertEquals(0, $res['total']);

        //for user or group
        $cases = $this->createSelfServiceUserOrGroup();

        $unassigned = new Unassigned();
        $unassigned->setUserUid($cases['taskUser']->USR_UID);
        $unassigned->setUserId($cases['delegation']->USR_ID);

        $res = $unassigned->getCustomListCount($caseList->CAL_ID, 'unassigned');

        //assertions
        $this->assertArrayHasKey('label', $res);
        $this->assertArrayHasKey('name', $res);
        $this->assertArrayHasKey('description', $res);
        $this->assertArrayHasKey('tableName', $res);
        $this->assertArrayHasKey('total', $res);

        $this->assertEquals($additionalTables->ADD_TAB_NAME, $res['tableName']);
        $this->assertEquals(0, $res['total']);
    }

    /**
     * It tests the getCasesRisk() method with ontime filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCasesRisk()
     * @test
     */
    public function it_should_test_get_cases_risk_on_time()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff1Day = new DateInterval('P1D');
        $diff2Days = new DateInterval('P2D');
        $user = factory(User::class)->create();
        $process1 = factory(Process::class)->create([
            'CATEGORY_ID' => 2
        ]);
        $application = factory(Application::class)->create([
            'APP_STATUS_ID' => 2
        ]);
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'PRO_ID' => $process1->PRO_ID,
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'PRO_ID' => $process1->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $date->add($diff1Day),
            'DEL_TASK_DUE_DATE' => $date->add($diff2Days)
        ]);
        $unassigned = new Unassigned();
        $unassigned->setUserId($user->USR_ID);
        $unassigned->setUserUid($user->USR_UID);

        $res = $unassigned->getCasesRisk($process1->PRO_ID);
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCasesRisk() method with at risk filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCasesRisk()
     * @test
     */
    public function it_should_test_get_cases_risk_at_risk()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff2Days = new DateInterval('P2D');
        $user = factory(User::class)->create();
        $process1 = factory(Process::class)->create([
            'CATEGORY_ID' => 2
        ]);
        $application = factory(Application::class)->create([
            'APP_STATUS_ID' => 2
        ]);
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'PRO_ID' => $process1->PRO_ID,
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'PRO_ID' => $process1->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $currentDate,
            'DEL_TASK_DUE_DATE' => $date->add($diff2Days)
        ]);
        $unassigned = new Unassigned();
        $unassigned->setUserId($user->USR_ID);
        $unassigned->setUserUid($user->USR_UID);

        $res = $unassigned->getCasesRisk($process1->PRO_ID, null, null, 'AT_RISK');
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getCasesRisk() method with overdue filter
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCasesRisk()
     * @test
     */
    public function it_should_test_get_cases_risk_overdue()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff2Days = new DateInterval('P2D');
        $user = factory(User::class)->create();
        $process1 = factory(Process::class)->create([
            'CATEGORY_ID' => 2
        ]);
        $application = factory(Application::class)->create([
            'APP_STATUS_ID' => 2
        ]);
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'PRO_ID' => $process1->PRO_ID,
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'PRO_ID' => $process1->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $currentDate,
            'DEL_TASK_DUE_DATE' => $date->sub($diff2Days)
        ]);
        $unassigned = new Unassigned();
        $unassigned->setUserId($user->USR_ID);
        $unassigned->setUserUid($user->USR_UID);

        $res = $unassigned->getCasesRisk($process1->PRO_ID, null, null, 'OVERDUE');
        $this->assertCount(1, $res);
    }

    /**
     * This the getCounterMetrics method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getCounterMetrics()
     * @test
     */
    public function it_tests_get_counter_metrics()
    {
        $this->createSelfServiceUserOrGroup();
        $unassigned = new Unassigned;
        $result = $unassigned->getCounterMetrics();
        $this->assertTrue($result > 0);
    }
}
