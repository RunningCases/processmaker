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
     * Method set up.
     */
    public function setUp()
    {
        parent::setUp();
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
                'DEL_DELEGATE_DATE' => date('Y-m-d H:m:s', strtotime("-$i year"))
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
                'ASSIGNEE_ID' => ($userAssignee) ? $user->USR_ID: $group->GRP_ID,
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
                'DEL_DELEGATE_DATE' => date('Y-m-d H:m:s', strtotime("-$i year"))
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
                'DEL_DELEGATE_DATE' => date('Y-m-d H:m:s', strtotime("-$i year"))
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
        $unassigned->setUserUid($casesUser['taskUser']->USR_UID);
        $unassigned->setUserId($casesUser['delegation']->USR_ID);
        $result = $unassigned->getCounter();
        $this->assertNotEmpty($result);
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
        //Review the count self-service
        $unassigned = new Unassigned;
        $unassigned->setUserUid($casesUser['user']->USR_UID);
        $unassigned->setUserId($casesUser['delegation']->USR_ID);
        $result = $unassigned->getCounter();
        $this->assertNotEmpty($result);
        $unassigned = new Unassigned;
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
        // Set the user UID
        $unassigned->setUserUid($cases['taskUser']->USR_UID);
        $unassigned->setUserId($cases['delegation']->USR_ID);
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
        $cases = $this->createSelfServiceUserOrGroup();
        $usrUid = $cases['taskUser']->USR_UID;
        $usrId = $cases['delegation']->USR_ID;
        $title = $cases['delegation']->DEL_TITLE;
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
}