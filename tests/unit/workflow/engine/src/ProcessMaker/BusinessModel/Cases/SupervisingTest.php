<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Supervising;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
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
    use DatabaseTransactions;

    /**
     * Method set up.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Create supervising cases factories
     *
     * @param string
     *
     * @return array
     */
    public function createSupervising()
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
        factory(Delegation::class)->create([
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
            'DEL_PREVIOUS' => 0
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
            'DEL_PREVIOUS' => 1
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
            'DEL_PREVIOUS' => 0
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
            'DEL_PREVIOUS' => 1
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
            'DEL_PREVIOUS' => 0
        ]);
        $delegation = factory(Delegation::class)->create([
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
            'DEL_PREVIOUS' => 1
        ]);

        // Create the register in the ProcessUser table
        factory(ProcessUser::class)->create(
            [
                'PRO_UID' => $process->PRO_UID,
                'USR_UID' => $user->USR_UID,
                'PU_TYPE' => 'SUPERVISOR'
            ]
        );

        return $delegation;
    }

    /**
     * Create many supervising cases for one user
     * 
     * @param int
     * @return object
     */
    public function createMultipleSupervising($cases)
    {
        $user = factory(\ProcessMaker\Model\User::class)->create();

        for ($i = 0; $i < $cases; $i = $i + 1) {
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
            factory(Delegation::class)->create([
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
                'DEL_PREVIOUS' => 0
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
                'DEL_PREVIOUS' => 1
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
                'DEL_PREVIOUS' => 0
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
                'DEL_PREVIOUS' => 1
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
                'DEL_PREVIOUS' => 0
            ]);
            $delegation = factory(Delegation::class)->create([
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
                'DEL_PREVIOUS' => 1
            ]);

            // Create the register in the ProcessUser table
            factory(ProcessUser::class)->create(
                [
                    'PRO_UID' => $process->PRO_UID,
                    'USR_UID' => $user->USR_UID,
                    'PU_TYPE' => 'SUPERVISOR'
                ]
            );
        }
        return $user;
    }

    /**
     * Tests the getData() method when the user is a supervisor of the process(es)
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @test
     */
    public function it_should_test_the_get_data_method_when_the_user_is_supervisor()
    {
        $cases = $this->createSupervising();
        // Instance the Supervising class
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($cases->USR_UID);
        // Set the user
        $supervising->setUserId($cases->USR_ID);
        // Get the data
        $result = $supervising->getData();
        // Asserts the result contains 3 registers
        $this->assertCount(3, $result);
    }

    /**
     * Tests the getData() method when the user belongs to a group supervisor
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @test
     */
    public function it_should_test_the_get_data_method_when_the_user_belong_to_a_group_supervisor()
    {
        $cases = $this->createSupervising();
        // Instance the Supervising object
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($cases->USR_UID);
        // Set the user ID
        $supervising->setUserId($cases->USR_ID);
        // Get the data
        $result = $supervising->getData();
        // Asserts the result contains 3 registers
        $this->assertCount(3, $result);
    }

    /**
     * Tests the getData() method when the user is not a supervisor neither belongs to a group supervisor 
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @test
     */
    public function it_should_test_the_get_data_method_when_the_user_is_not_supervisor()
    {
        $user = factory(User::class)->create();
        $cases = $this->createSupervising();
        // Instance the Supervising object
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($user->USR_UID);
        // Set the user
        $supervising->setUserId($user->USR_ID);
        // Get the data
        $result = $supervising->getData();
        // Asserts the result
        $this->assertEmpty($result);
    }

    /**
     * Tests the specific filter setCaseNumber
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setCaseNumber()
     * @test
     */
    public function it_filter_by_app_number()
    {
        $cases = $this->createSupervising();
        // Instance the Supervising object
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($cases->USR_UID);
        // Set the user
        $supervising->setUserId($cases->USR_ID);
        // Set the case number
        $supervising->setCaseNumber($cases->APP_NUMBER);
        // Get the data
        $result = $supervising->getData();
        // Asserts the result contains 3 registers
        $this->assertCount(1, $result);
        // Asserts that the result contains the app number searched
        $this->assertContains($cases->APP_NUMBER, $result[0]);
    }

    /**
     * Tests the specific filter setCasesNumbers
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setCasesNumbers()
     * @test
     */
    public function it_filter_by_specific_cases()
    {
        $cases = $this->createSupervising();
        // Instance the Supervising object
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($cases->USR_UID);
        // Set the user
        $supervising->setUserId($cases->USR_ID);
        // Set the case numbers
        $supervising->setCasesNumbers([$cases->APP_NUMBER]);
        // Get the data
        $result = $supervising->getData();
        // Asserts the result contains 3 registers
        $this->assertCount(1, $result);
        // Asserts that the result contains the app number searched
        $this->assertContains($cases->APP_NUMBER, $result[0]);
    }

    /**
     * Tests the specific filter setRangeCasesFromTo
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setRangeCasesFromTo()
     * @test
     */
    public function it_filter_by_range_cases()
    {
        $cases = $this->createSupervising();
        // Instance the Supervising object
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($cases->USR_UID);
        // Set the user ID
        $supervising->setUserId($cases->USR_ID);
        // Set the range of case numbers
        $rangeOfCases = $cases->APP_NUMBER . "-" . $cases->APP_NUMBER;
        $supervising->setRangeCasesFromTo([$rangeOfCases]);
        // Get the data
        $result = $supervising->getData();
        // Asserts the result contains 3 registers
        $this->assertCount(1, $result);
        // Asserts that the result contains the app number searched
        $this->assertContains($cases->APP_NUMBER, $result[0]);
    }

    /**
     * Tests the specific filter caseTitle
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setCaseTitle()
     * @test
     */
    public function it_filter_by_thread_title()
    {
        // Create factories related to the to_do cases
        $cases = $this->createSupervising();
        $usrUid = $cases->USR_UID;
        $usrId = $cases->USR_ID;
        $title = $cases->DEL_TITLE;
        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();
        // Create new Inbox object
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($usrUid);
        // Set the user ID
        $supervising->setUserId($usrId);
        // Set the title
        $supervising->setCaseTitle($title);
        // Get the data
        $result = $supervising->getData();
        // Asserts
        $this->assertCount(1, $result);
    }

    /**
     * Tests the specific filter process
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setProcessId()
     * @test
     */
    public function it_filter_by_process()
    {
        $cases = $this->createSupervising();
        // Instance the Supervising object
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($cases['USR_UID']);
        // Set the user ID
        $supervising->setUserId($cases['USR_ID']);
        // Set the process
        $supervising->setProcessId($cases['PRO_ID']);
        // Get the data
        $result = $supervising->getData();
        $this->assertCount(3, $result);
    }

    /**
     * Tests the specific filter task
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setTaskId()
     * @test
     */
    public function it_filter_by_task()
    {
        $cases = $this->createSupervising();
        // Instance the Supervising object
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($cases['USR_UID']);
        // Set the user ID
        $supervising->setUserId($cases['USR_ID']);
        // Set the task
        $supervising->setTaskId($cases['TAS_ID']);
        // Get the data
        $result = $supervising->getData();
        $this->assertCount(3, $result);
    }

    /**
     * Tests the specific filter status
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setCaseStatus()
     * @test
     */
    public function it_filter_by_status()
    {
        $cases = $this->createSupervising();
        // Instance the Supervising object
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($cases['USR_UID']);
        // Set the user ID
        $supervising->setUserId($cases['USR_ID']);
        // Set the task
        $supervising->setCaseStatus('TO_DO');
        // Get the data
        $result = $supervising->getData();
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getData the specific filter setStartCaseFrom and getStartCaseTo
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setStartCaseFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setStartCaseTo()
     * @test
     */
    public function it_filter_by_start_date()
    {
        $cases = $this->createSupervising();
        // Instance the Supervising object
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($cases['USR_UID']);
        // Set the user ID
        $supervising->setUserId($cases['USR_ID']);
        // Set the range of dates
        $date = date('Y-m-d');
        $supervising->setStartCaseFrom($date);
        $supervising->setStartCaseTo($date);
        // Get the data
        $result = $supervising->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEmpty($result);
    }

    /**
     * It tests the getData the specific filter setFinishCaseFrom and setFinishCaseTo
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setFinishCaseFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setFinishCaseTo()
     * @test
     */
    public function it_filter_by_finish_date()
    {
        $cases = $this->createSupervising();
        // Instance the Supervising object
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($cases['USR_UID']);
        // Set the user ID
        $supervising->setUserId($cases['USR_ID']);
        // Set the range of dates
        $date = date('Y-m-d');
        $supervising->setFinishCaseFrom($date);
        $supervising->setFinishCaseTo($date);
        // Get the data
        $result = $supervising->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEmpty($result);
    }

    /**
     * Tests the order by value
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setOrderByColumn()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @test
     */
    public function it_order_by_column()
    {
        // Create factories related to the supervising cases
        $cases = $this->createSupervising();
        $columnsView = [
            'APP_NUMBER',
            'DEL_TITLE',
            'PRO_TITLE',
            'TAS_TITLE',
            'APP_CREATE_DATE',
            'APP_FINISH_DATE'
        ];
        $index = array_rand($columnsView);
        // Instance the Supervising object
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($cases['USR_UID']);
        // Set the user ID
        $supervising->setUserId($cases['USR_ID']);
        // Set the order by value
        $supervising->setOrderByColumn($columnsView[$index]);
        // Get the data
        $result = $supervising->getData();
        $this->assertNotEmpty($result);
    }

    /**
     * Tests the getCounter() method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getCounter()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::setUserId()
     * @test
     */
    public function it_should_count_the_data()
    {
        $cases = $this->createSupervising();
        // Instance the Supervising object
        $supervising = new Supervising();
        // Set the user UID
        $supervising->setUserUid($cases->USR_UID);
        // Set the user
        $supervising->setUserId($cases->USR_ID);
        // Get the data
        $result = $supervising->getCounter();
        // Assert the counter
        $this->assertEquals(3, $result);
    }

    /**
     * It tests the getPagingCounters() method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Supervising::getPagingCounters()
     * @test
     */
    public function it_should_test_get_paging_counters_method()
    {
        $cases = $this->createMultipleSupervising(3);
        $supervising = new Supervising();
        $supervising->setUserId($cases->USR_ID);
        $supervising->setUserUid($cases->USR_UID);
        // Get the count
        $result = $supervising->getPagingCounters();
        $this->assertEquals(3, $result);
    }
}
