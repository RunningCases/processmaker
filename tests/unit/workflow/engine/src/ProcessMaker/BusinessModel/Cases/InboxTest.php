<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\BusinessModel\Cases\Inbox;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class InboxTest
 *
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Inbox
 */
class InboxTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * It tests the getData method without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
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
        ]);
        //Create the register in delegation
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID
        ]);
        //Create new Inbox object
        $inbox = new Inbox();
        //Set the user UID
        $inbox->setUserUid($user->USR_UID);
        //Set the user ID
        $inbox->setUserId($user->USR_ID);
        //Set OrderBYColumn value
        $inbox->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        //Call to getData method
        $res = $inbox->getData();
        //This assert that the expected numbers of results are returned
        $this->assertEquals(10, count($res));
    }

    /**
     * It tests the getData method with Risk Filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_it_should_test_get_data_method_with_Risk_Filter()
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

        //Create the register in delegation
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID,
            'DEL_RISK_DATE' => '2019-06-07 12:30:58'
        ]);

        //Create new Inbox object
        $inbox = new Inbox();

        //Set the user UID
        $inbox->setUserUid($user->USR_UID);

        //Set the user ID
        $inbox->setUserId($user->USR_ID);

        //Set OrderBYColumn value
        $inbox->setOrderByColumn('APP_DELEGATION.APP_NUMBER');

        //Set setRiskStatus value
        $inbox->setRiskStatus('ON_TIME');
        $res = $inbox->getData();

        //This asserts that no cases are in ON_TIME status
        $this->assertEmpty($res);

        //Set setRiskStatus value
        $inbox->setRiskStatus('OVERDUE');

        //Call to getData method
        $res = $inbox->getData();

        //This asserts that there are cases in AT_RISK status
        $this->assertNotEmpty($res);

        //Set setRiskStatus value
        $inbox->setRiskStatus('AT_RISK');

        //Call to getData method
        $res = $inbox->getData();

        //This asserts that no cases are in AT_RISK status
        $this->assertEmpty($res);
    }

    /**
     * It tests the getData method with Category Filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_it_should_test_get_data_method_with_Category_Filter()
    {
        //Create process
        $process = factory(Process::class)->create(
            ['PRO_CATEGORY' => '248565910552bd7d6006458065223611']
        );

        //Create user
        $user = factory(User::class)->create();

        //Create a task
        $task = factory(Task::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID
        ]);

        //Create the register in delegation
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID
        ]);

        //Create new Inbox object
        $inbox = new Inbox();

        //Set the user UID
        $inbox->setUserUid($user->USR_UID);

        //Set the user ID
        $inbox->setUserId($user->USR_ID);

        //Set OrderBYColumn value
        $inbox->setOrderByColumn('APP_DELEGATION.APP_NUMBER');

        //Set Category value
        $inbox->setCategoryUid('248565910552bd7d6006458065223611');

        //Call to getData method
        $res = $inbox->getData();

        //
        $this->assertEquals(10, count($res));
    }

    /**
     * It tests the getData method with Process Filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_it_should_test_get_data_method_with_Process_Filter()
    {
        //Create process
        $process = factory(Process::class, 2)->create();

        //Create user
        $user = factory(User::class)->create();

        //Create a task
        $task = factory(Task::class)->create([
            'PRO_UID' => $process[0]->PRO_UID,
            'PRO_ID' => $process[0]->PRO_ID
        ]);

        //Create the register in delegation relate to self-service
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process[0]->PRO_ID
        ]);

        $inbox = new Inbox();
        $inbox->setUserUid($user->USR_UID);
        $inbox->setUserId($user->USR_ID);
        $inbox->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        $inbox->setProcessId($process[1]->PRO_ID);
        $res = $inbox->getData();
        $this->assertEmpty($res);
        $inbox->setProcessId($process[0]->PRO_ID);
        $res = $inbox->getData();
        $this->assertEquals(10, count($res));
    }

    /**
     * It tests the getData method using OrderBy
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_should_return_inbox_sort_by_case_number()
    {
        //Create process
        $process = factory(Process::class)->create();

        //Create user
        $user = factory(User::class)->create();

        //Create tasks
        $task = factory(Task::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID
        ]);

        //Create the register in delegation
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID
        ]);

        $inbox = new Inbox();
        $inbox->setUserUid($user->USR_UID);
        $inbox->setUserId($user->USR_ID);
        $inbox->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        $inbox->setOrderDirection('DESC');
        $res = $inbox->getData();
        // This asserts the order is for APP_NUMBER from highest to lowest
        $this->assertLessThan($res[0]['APP_NUMBER'], $res[1]['APP_NUMBER']);

        $inbox->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        $inbox->setOrderDirection('ASC');
        $res = $inbox->getData();
        // This asserts the order is for APP_NUMBER from highest to lowest
        $this->assertGreaterThan($res[0]['APP_NUMBER'], $res[1]['APP_NUMBER']);
    }

    /**
     * It tests the getData method using OrderBy
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_should_return_inbox_sort_by_task_title()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        for ($i = 1; $i <= 2; $i++) {
            //Create tasks
            $task = factory(Task::class)->create([
                'PRO_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID
            ]);
            //Create the register in delegation
            factory(Delegation::class, 10)->create([
                'TAS_ID' => $task->TAS_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_UID' => $user->USR_UID,
                'USR_ID' => $user->USR_ID,
                'PRO_ID' => $process->PRO_ID
            ]);
        }

        $inbox = new Inbox();
        $inbox->setUserUid($user->USR_UID);
        $inbox->setUserId($user->USR_ID);
        $inbox->setOrderByColumn('TASK.TAS_ID');
        $inbox->setOrderDirection('DESC');
        $res = $inbox->getData();
        $this->assertLessThanOrEqual($res[0]['TAS_ID'], $res[1]['TAS_ID']);

        $inbox->setOrderByColumn('TASK.TAS_ID');
        $inbox->setOrderDirection('ASC');
        $res = $inbox->getData();
        $this->assertGreaterThanOrEqual($res[0]['TAS_ID'], $res[1]['TAS_ID']);
    }

    /**
     * It tests the getData method using OrderBy
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_should_return_inbox_sort_by_case_title()
    {
        //Create process
        $process = factory(Process::class)->create();

        //Create user
        $user = factory(User::class)->create();

        //Create tasks
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
        ]);

        //Create the register in delegation
        factory(Delegation::class, 20)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID
        ]);

        $inbox = new Inbox();
        $inbox->setUserUid($user->USR_UID);
        $inbox->setUserId($user->USR_ID);
        $inbox->setOrderByColumn('APP_TITLE');
        $inbox->setOrderDirection('DESC');
        $res = $inbox->getData();
        // This asserts the order is for APP_TITLE from highest to lowest
        $this->assertLessThan($res[0]['APP_TITLE'], $res[1]['APP_TITLE']);

        $inbox->setOrderByColumn('APP_TITLE');
        $inbox->setOrderDirection('ASC');
        $res = $inbox->getData();
        // This asserts the order is for APP_TITLE from highest to lowest
        $this->assertGreaterThan($res[0]['APP_TITLE'], $res[1]['APP_TITLE']);
    }

    /**
     * It tests the getData method using OrderBy
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_should_return_inbox_sort_by_process()
    {
        //Create process
        $process1 = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();

        //Create user
        $user = factory(User::class)->create();

        //Create tasks
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
        ]);

        //Create the register in delegation
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID
        ]);
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID
        ]);

        $inbox = new Inbox();
        $inbox->setUserUid($user->USR_UID);
        $inbox->setUserId($user->USR_ID);
        $inbox->setOrderByColumn('PROCESS.PRO_ID');
        $inbox->setOrderDirection('DESC');
        $res = $inbox->getData();
        // This asserts the order is for PRO_ID from highest to lowest
        $this->assertLessThanOrEqual($res[0]['PRO_ID'], $res[1]['PRO_ID']);

        $inbox->setOrderByColumn('PROCESS.PRO_ID');
        $inbox->setOrderDirection('ASC');
        $res = $inbox->getData();
        // This asserts the order is for PRO_ID from highest to lowest
        $this->assertGreaterThanOrEqual($res[0]['PRO_ID'], $res[1]['PRO_ID']);
    }

    /**
     * It tests the getData method using OrderBy
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_should_return_inbox_sort_by_due_date()
    {
        //Create process
        $process1 = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();

        //Create user
        $user = factory(User::class)->create();

        //Create tasks
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
        ]);

        //Create the register in delegation
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID
        ]);
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID
        ]);

        $inbox = new Inbox();
        $inbox->setUserUid($user->USR_UID);
        $inbox->setUserId($user->USR_ID);
        $inbox->setOrderByColumn('DEL_TASK_DUE_DATE');
        $inbox->setOrderDirection('DESC');
        $res = $inbox->getData();
        // This asserts the order is for DEL_TASK_DUE_DATE from highest to lowest
        $this->assertLessThanOrEqual($res[0]['DEL_TASK_DUE_DATE'], $res[1]['DEL_TASK_DUE_DATE']);

        $inbox->setOrderByColumn('DEL_TASK_DUE_DATE');
        $inbox->setOrderDirection('ASC');
        $res = $inbox->getData();
        // This asserts the order is for DEL_TASK_DUE_DATE from highest to lowest
        $this->assertGreaterThanOrEqual($res[0]['DEL_TASK_DUE_DATE'], $res[1]['DEL_TASK_DUE_DATE']);
    }

    /**
     * It tests the getData method using OrderBy
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_should_return_inbox_sort_by_last_modified()
    {
        //Create process
        $process1 = factory(Process::class)->create();
        $process2 = factory(Process::class)->create();

        //Create user
        $user = factory(User::class)->create();

        //Create tasks
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
        ]);

        //Create the register in delegation
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID
        ]);
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID
        ]);

        $inbox = new Inbox();
        $inbox->setUserUid($user->USR_UID);
        $inbox->setUserId($user->USR_ID);
        $inbox->setOrderByColumn('APP_UPDATE_DATE');
        $inbox->setOrderDirection('DESC');
        $res = $inbox->getData();
        // This asserts the order is for APP_UPDATE_DATE from highest to lowest
        $this->assertLessThanOrEqual($res[0]['APP_UPDATE_DATE'], $res[1]['APP_UPDATE_DATE']);

        $inbox->setOrderByColumn('APP_UPDATE_DATE');
        $inbox->setOrderDirection('ASC');
        $res = $inbox->getData();
        // This asserts the order is for APP_UPDATE_DATE from highest to lowest
        $this->assertGreaterThanOrEqual($res[0]['APP_UPDATE_DATE'], $res[1]['APP_UPDATE_DATE']);
    }

    /**
     * It tests the getData method with pager
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getData()
     * @test
     */
    public function it_it_should_test_get_data_method_with_pager()
    {
        //Create process
        $process = factory(Process::class)->create(
            ['PRO_CATEGORY' => '248565910552bd7d6006458065223611']
        );

        //Create user
        $user = factory(User::class)->create();

        //Create a task
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
        ]);

        //Create the register in delegation relate to self-service
        factory(Delegation::class, 50)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID
        ]);

        $inbox = new Inbox();
        $inbox->setUserUid($user->USR_UID);
        $inbox->setUserId($user->USR_ID);
        $inbox->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        $inbox->setOffset(5);
        $inbox->setLimit(2);
        $res = $inbox->getData();

        $this->assertEquals(2, count($res));
    }

    /**
     * It tests the getCounter method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Inbox::getCounter()
     * @test
     */
    public function it_should_test_the_counter_for_list_inbox()
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

        //Create the register in delegation relate to self-service
        factory(Delegation::class, 10)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process->PRO_ID
        ]);

        //Create the Inbox object
        $inbox = new Inbox();
        $inbox->setUserId($user->USR_ID);
        $res = $inbox->getCounter();

        //Assert the result of getCounter method
        $this->assertEquals(10, $res);
    }
}