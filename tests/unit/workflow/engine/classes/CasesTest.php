<?php

namespace Tests\unit\workflow\engine\classes;

use Cases;
use Exception;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Step;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use ProcessMaker\Model\User;
use Tests\TestCase;

class CasesTest extends TestCase
{
    /**
     * Test getNextStep method with no steps
     *
     * @covers \Cases::getNextStep()
     * @test
     */
    public function it_should_test_get_next_step_method()
    {
        $process = Process::factory()->create();
        $application = Application::factory()->create(['PRO_UID' => $process->PRO_UID]);
        $appDelegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => $appDelegation->DEL_INDEX
        ]);
        $cases = new Cases();
        $res = $cases->getNextStep($process->PRO_UID, $application->APP_UID, $appDelegation->DEL_INDEX);

        $this->assertCount(4, $res);
    }

    /**
     * Tests the getNextStep method with step
     *
     * @covers \Cases::getNextStep()
     * @test
     */
    public function it_should_test_get_next_step_method_position()
    {
        $process = Process::factory()->create();
        $application = Application::factory()->create(['PRO_UID' => $process->PRO_UID]);
        $appDelegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => $appDelegation->DEL_INDEX
        ]);
        Step::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $appDelegation->TAS_UID,
            'STEP_POSITION' => 2,
            'STEP_CONDITION' => '1 == 1'
        ]);
        $cases = new Cases();
        $res = $cases->getNextStep($process->PRO_UID, $application->APP_UID, $appDelegation->DEL_INDEX, 1);
        $this->assertCount(4, $res);
    }

    /**
     * Tests the getNextStep method with output document
     *
     * @covers \Cases::getNextStep()
     * @test
     */
    public function it_should_test_get_next_step_method_output_document()
    {
        $process = Process::factory()->create();
        $application = Application::factory()->create(['PRO_UID' => $process->PRO_UID]);
        $appDelegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => $appDelegation->DEL_INDEX
        ]);
        Step::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $appDelegation->TAS_UID,
            'STEP_POSITION' => 2,
            'STEP_CONDITION' => '1 == 1',
            'STEP_TYPE_OBJ' => 'OUTPUT_DOCUMENT'
        ]);
        $cases = new Cases();
        $res = $cases->getNextStep($process->PRO_UID, $application->APP_UID, $appDelegation->DEL_INDEX, 1);
        $this->assertCount(4, $res);
    }

    /**
     * Tests the getNextStep method with input document
     *
     * @covers \Cases::getNextStep()
     * @test
     */
    public function it_should_test_get_next_step_method_input_document()
    {
        $process = Process::factory()->create();
        $application = Application::factory()->create(['PRO_UID' => $process->PRO_UID]);
        $appDelegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => $appDelegation->DEL_INDEX
        ]);
        Step::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $appDelegation->TAS_UID,
            'STEP_POSITION' => 2,
            'STEP_CONDITION' => '1 == 1',
            'STEP_TYPE_OBJ' => 'INPUT_DOCUMENT'
        ]);
        $cases = new Cases();
        $res = $cases->getNextStep($process->PRO_UID, $application->APP_UID, $appDelegation->DEL_INDEX, 1);
        $this->assertCount(4, $res);
    }

    /**
     * Tests the getNextStep method with external document
     *
     * @covers \Cases::getNextStep()
     * @test
     */
    public function it_should_test_get_next_step_method_external()
    {
        $process = Process::factory()->create();
        $application = Application::factory()->create(['PRO_UID' => $process->PRO_UID]);
        $appDelegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => $appDelegation->DEL_INDEX
        ]);
        Step::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $appDelegation->TAS_UID,
            'STEP_POSITION' => 2,
            'STEP_CONDITION' => '1 == 1',
            'STEP_TYPE_OBJ' => 'EXTERNAL'
        ]);
        $cases = new Cases();
        $res = $cases->getNextStep($process->PRO_UID, $application->APP_UID, $appDelegation->DEL_INDEX, 1);
        $this->assertCount(4, $res);
    }

    /**
     * Tests the getNextStep method with message step
     *
     * @covers \Cases::getNextStep()
     * @test
     */
    public function it_should_test_get_next_step_method_message()
    {
        $process = Process::factory()->create();
        $application = Application::factory()->create(['PRO_UID' => $process->PRO_UID]);
        $appDelegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => $appDelegation->DEL_INDEX
        ]);
        Step::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $appDelegation->TAS_UID,
            'STEP_POSITION' => 2,
            'STEP_CONDITION' => '1 == 1',
            'STEP_TYPE_OBJ' => 'MESSAGE'
        ]);
        $cases = new Cases();
        $res = $cases->getNextStep($process->PRO_UID, $application->APP_UID, $appDelegation->DEL_INDEX, 1);
        $this->assertCount(4, $res);
    }

    /**
     * Tests the getNextStep method when the step does not exist
     *
     * @covers \Cases::getNextStep()
     * @test
     */
    public function it_should_test_get_next_step_method_step_does_not_exists()
    {
        $process = Process::factory()->create();
        $application = Application::factory()->create(['PRO_UID' => $process->PRO_UID]);
        $appDelegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => $appDelegation->DEL_INDEX
        ]);
        $cases = new Cases();
        $this->expectExceptionMessage("**ID_STEP_DOES_NOT_EXIST**");
        $res = $cases->getNextStep($process->PRO_UID, $application->APP_UID, $appDelegation->DEL_INDEX, 1);
    }

    /**
     * Tests the getNextStep method when there is an exception
     *
     * @covers \Cases::getNextStep()
     * @test
     */
    public function it_should_test_get_next_step_method_step_exception()
    {
        $cases = new Cases();
        $this->expectException(Exception::class);
        $res = $cases->getNextStep();
    }

    /**
     * Tests the getNextStep method when the result is false
     *
     * @covers \Cases::getNextStep()
     * @test
     */
    public function it_should_test_get_next_step_method_step_false()
    {
        $process = Process::factory()->create();
        $application = Application::factory()->create();
        $appDelegation = Delegation::factory()->create();
        $cases = new Cases();
        $res = $cases->getNextStep($process->PRO_UID, $application->APP_UID, $appDelegation->DEL_INDEX);
        $this->assertFalse($res);
    }

    /**
     * Tests the getNextStep method when there is a gmail account
     *
     * @covers \Cases::getNextStep()
     * @test
     */
    public function it_should_test_get_next_step_method_gmail()
    {
        $_SESSION['gmail'] = '';
        $process = Process::factory()->create();
        $application = Application::factory()->create(['PRO_UID' => $process->PRO_UID]);
        $appDelegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => $appDelegation->DEL_INDEX
        ]);
        Step::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $appDelegation->TAS_UID,
            'STEP_POSITION' => 2,
            'STEP_CONDITION' => '1 == 1',
            'STEP_TYPE_OBJ' => 'MESSAGE'
        ]);
        $cases = new Cases();
        $res = $cases->getNextStep($process->PRO_UID, $application->APP_UID, $appDelegation->DEL_INDEX, 1);
        $this->assertCount(4, $res);
    }

    /**
     * Tests the getNextStep method when there is a gmail account related to the next step
     *
     * @covers \Cases::getNextStep()
     * @test
     */
    public function it_should_test_get_next_step_method_gmail_nextstep()
    {
        $_SESSION['gmail'] = '';
        $process = Process::factory()->create();
        $application = Application::factory()->create(['PRO_UID' => $process->PRO_UID]);
        $appDelegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => $appDelegation->DEL_INDEX
        ]);
        Step::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $appDelegation->TAS_UID,
            'STEP_POSITION' => 1,
            'STEP_CONDITION' => '1 == 1',
            'STEP_TYPE_OBJ' => 'MESSAGE'
        ]);
        $cases = new Cases();
        $res = $cases->getNextStep($process->PRO_UID, $application->APP_UID, $appDelegation->DEL_INDEX, 1);
        $this->assertCount(4, $res);
    }

    /**
     * Tests the getNextStep method when the step condition is empty
     *
     * @covers \Cases::getNextStep()
     * @test
     */
    public function it_should_test_get_next_step_method_condition_empty()
    {
        $_SESSION['gmail'] = '';
        $process = Process::factory()->create();
        $application = Application::factory()->create(['PRO_UID' => $process->PRO_UID]);
        $appDelegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => $appDelegation->DEL_INDEX
        ]);
        Step::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $appDelegation->TAS_UID,
            'STEP_POSITION' => 2,
            'STEP_CONDITION' => '',
            'STEP_TYPE_OBJ' => 'MESSAGE'
        ]);
        $cases = new Cases();
        $res = $cases->getNextStep($process->PRO_UID, $application->APP_UID, $appDelegation->DEL_INDEX, 1);
        $this->assertCount(4, $res);
    }

    /**
     * Test the getStartCases method
     *
     * @covers \Cases::getStartCases()
     * @test
     */
    public function it_should_test_get_start_cases()
    {
        // Creating a process with initial tasks
        $process = Process::factory()->create();
        $user = User::factory()->create();
        $normalTask = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
            'TAS_START' => 'TRUE'
        ]);
        $webEntryTask = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
            'TAS_START' => 'TRUE',
            'TAS_TYPE' => 'WEBENTRYEVENT'
        ]);
        TaskUser::factory()->create([
            'TAS_UID' => $normalTask->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);
        TaskUser::factory()->create([
            'TAS_UID' => $webEntryTask->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);

        // Instance class Cases
        $cases = new Cases();

        // Get all initial tasks
        $startingTasks = $cases->getStartCases($user->USR_UID);
        $this->assertCount(3, $startingTasks);

        // Get initial tasks without dummy tasks
        $startingTasks = $cases->getStartCases($user->USR_UID, true);
        $this->assertCount(2, $startingTasks);
    }

    /**
     * Tests the getTo method when the task assign type is BALANCED
     * 
     * @covers \Cases::getTo()
     * @test
     */
    public function it_shoult_test_the_get_to_method_with_default_tas_assign_type()
    {
        $task = Task::factory()->create();
        $user = User::factory()->create([
            'USR_EMAIL' => 'test@test.com'
        ]);

        // Instance class Cases
        $cases = new Cases();
        $result = $cases->getTo($task->TAS_UID, $user->USR_UID, '');
        $this->assertNotEmpty($result);
        $this->assertMatchesRegularExpression("/{$user->USR_EMAIL}/", $result["to"]);
    }

    /**
     * Tests the getTo method when the task assign type is SELF_SERVICE
     * 
     * @covers \Cases::getTo()
     * @test
     */
    public function it_shoult_test_the_get_to_method_with_self_service_tas_assign_type()
    {
        $process = Process::factory()->create();

        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_ASSIGN_TYPE' => 'BALANCED'
        ]);
        $task2 = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE'
        ]);

        $user = User::factory()->create([
            'USR_EMAIL' => 'test@test.com'
        ]);
        $user2 = User::factory()->create([
            'USR_EMAIL' => 'test2@test2.com'
        ]);

        TaskUser::factory()->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);
        TaskUser::factory()->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user2->USR_UID
        ]);

        $application = Application::factory()->create([
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => "00000000000000000000000000000001",
            'APP_CUR_USER' => $user2->USR_UID
        ]);

        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 1,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_PREVIOUS' => 0,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => "00000000000000000000000000000001",
            'DEL_THREAD' => 1,
            'DEL_THREAD_STATUS' => 'CLOSED'
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 2,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_PREVIOUS' => 1,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'DEL_THREAD' => 2,
            'DEL_THREAD_STATUS' => 'OPEN'
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 3,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_PREVIOUS' => 1,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user2->USR_UID,
            'DEL_THREAD' => 3,
            'DEL_THREAD_STATUS' => 'OPEN'
        ]);

        $arrayData = [
            "SYS_LANG" => "en",
            "SYS_SKIN" => "neoclassic",
            "SYS_SYS" => "workflow",
            "APPLICATION" => $application->APP_UID,
            "PROCESS" => $process->PRO_UID,
            "TASK" => $task->TAS_UID,
            "INDEX" => "1",
            "USER_LOGGED" => "00000000000000000000000000000001",
            "USR_USERNAME" => "admin",
            "APP_NUMBER" => $application->APP_NUMBER,
            "PIN" => $application->APP_PIN,
            "TAS_ID" => $task->TAS_ID
        ];

        // Instance class Cases
        $cases = new Cases();
        $result = $cases->getTo($task2->TAS_UID, $user->USR_UID, $arrayData);

        // Asserts the result is not empty
        $this->assertNotEmpty($result);

        // Asserts the emails of both users are contained in the result
        $this->assertMatchesRegularExpression("/{$user->USR_EMAIL}/", $result["to"]);
        $this->assertMatchesRegularExpression("/{$user2->USR_EMAIL}/", $result["to"]);
    }

    /**
     * Tests the getTo method when the task assign type is MULTIPLE_INSTANCE
     * 
     * @covers \Cases::getTo()
     * @test
     */
    public function it_shoult_test_the_get_to_method_with_multiple_instance_tas_assign_type()
    {
        $process = Process::factory()->create();

        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_ASSIGN_TYPE' => 'BALANCED'
        ]);
        $task2 = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_ASSIGN_TYPE' => 'MULTIPLE_INSTANCE'
        ]);

        $user = User::factory()->create([
            'USR_EMAIL' => 'test@test.com'
        ]);
        $user2 = User::factory()->create([
            'USR_EMAIL' => 'test2@test2.com'
        ]);

        TaskUser::factory()->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);
        TaskUser::factory()->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user2->USR_UID
        ]);

        $application = Application::factory()->create([
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => "00000000000000000000000000000001",
            'APP_CUR_USER' => $user2->USR_UID
        ]);

        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 1,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_PREVIOUS' => 0,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => "00000000000000000000000000000001",
            'DEL_THREAD' => 1,
            'DEL_THREAD_STATUS' => 'CLOSED'
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 2,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_PREVIOUS' => 1,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'DEL_THREAD' => 2,
            'DEL_THREAD_STATUS' => 'OPEN'
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 3,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_PREVIOUS' => 1,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user2->USR_UID,
            'DEL_THREAD' => 3,
            'DEL_THREAD_STATUS' => 'OPEN'
        ]);

        $arrayData = [
            "SYS_LANG" => "en",
            "SYS_SKIN" => "neoclassic",
            "SYS_SYS" => "workflow",
            "APPLICATION" => $application->APP_UID,
            "PROCESS" => $process->PRO_UID,
            "TASK" => $task->TAS_UID,
            "INDEX" => "1",
            "USER_LOGGED" => "00000000000000000000000000000001",
            "USR_USERNAME" => "admin",
            "APP_NUMBER" => $application->APP_NUMBER,
            "PIN" => $application->APP_PIN,
            "TAS_ID" => $task->TAS_ID
        ];

        // Instance class Cases
        $cases = new Cases();
        $result = $cases->getTo($task2->TAS_UID, $user->USR_UID, $arrayData);

        // Asserts the result is not empty
        $this->assertNotEmpty($result);

        // Asserts the emails of both users are contained in the result
        $this->assertMatchesRegularExpression("/{$user->USR_EMAIL}/", $result["to"]);
    }

    /**
     * Tests the getTo method when the task assign type is MULTIPLE_INSTANCE_VALUE_BASED
     * 
     * @covers \Cases::getTo()
     * @test
     */
    public function it_shoult_test_the_get_to_method_with_multiple_instance_value_based_tas_assign_type()
    {
        $process = Process::factory()->create();

        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_ASSIGN_TYPE' => 'BALANCED'
        ]);
        $task2 = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_ASSIGN_TYPE' => 'MULTIPLE_INSTANCE_VALUE_BASED',
            'TAS_ASSIGN_VARIABLE' => '@@users'
        ]);

        $user = User::factory()->create([
            'USR_EMAIL' => 'test@test.com'
        ]);
        $user2 = User::factory()->create([
            'USR_EMAIL' => 'test2@test2.com'
        ]);

        TaskUser::factory()->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);
        TaskUser::factory()->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user2->USR_UID
        ]);

        $application = Application::factory()->create([
            'APP_STATUS_ID' => 2,
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => "00000000000000000000000000000001",
            'APP_CUR_USER' => $user2->USR_UID
        ]);

        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 1,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_PREVIOUS' => 0,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => "00000000000000000000000000000001",
            'DEL_THREAD' => 1,
            'DEL_THREAD_STATUS' => 'CLOSED'
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 2,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_PREVIOUS' => 1,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'DEL_THREAD' => 2,
            'DEL_THREAD_STATUS' => 'OPEN'
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 3,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_PREVIOUS' => 1,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user2->USR_UID,
            'DEL_THREAD' => 3,
            'DEL_THREAD_STATUS' => 'OPEN'
        ]);

        $arrayData = [
            "SYS_LANG" => "en",
            "SYS_SKIN" => "neoclassic",
            "SYS_SYS" => "workflow",
            "APPLICATION" => $application->APP_UID,
            "PROCESS" => $process->PRO_UID,
            "TASK" => $task->TAS_UID,
            "INDEX" => "1",
            "USER_LOGGED" => "00000000000000000000000000000001",
            "USR_USERNAME" => "admin",
            "APP_NUMBER" => $application->APP_NUMBER,
            "PIN" => $application->APP_PIN,
            "TAS_ID" => $task->TAS_ID,
            'users' => [$user->USR_UID, $user2->USR_UID]
        ];

        // Instance class Cases
        $cases = new Cases();
        $result = $cases->getTo($task2->TAS_UID, $user->USR_UID, $arrayData);

        // Asserts the result is not empty
        $this->assertNotEmpty($result);

        // Asserts the emails of both users are contained in the result
        $this->assertMatchesRegularExpression("/{$user->USR_EMAIL}/", $result["to"]);
    }
}
