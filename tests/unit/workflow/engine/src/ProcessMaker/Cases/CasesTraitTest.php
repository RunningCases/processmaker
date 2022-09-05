<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Cases;

use App\Jobs\RouteCase;
use Cases;
use G;
use Illuminate\Support\Facades\Queue;
use ProcessMaker\Model\AbeConfiguration;
use ProcessMaker\Model\AbeRequest;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Dynaform;
use ProcessMaker\Model\EmailServerModel;
use ProcessMaker\Model\InputDocument;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Route;
use ProcessMaker\Model\Step;
use ProcessMaker\Model\StepTrigger;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use ProcessMaker\Model\Triggers;
use ProcessMaker\Model\User;
use RBAC;
use Tests\TestCase;

class CasesTraitTest extends TestCase
{

    /**
     * Set up method.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Prepare initial derivation data.
     * @return object
     */
    private function prepareDerivationData()
    {
        $user = User::where('USR_ID', '=', 1)->first();

        $process = factory(Process::class)->create([
            'PRO_CREATE_USER' => $user->USR_UID
        ]);
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'BALANCED',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'BALANCED',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);


        $application = factory(Application::class)->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $appDelegation = factory(Delegation::class)->create([
            'USR_UID' => $user->USR_UID,
            'PRO_UID' => $process->PRO_UID,
            'APP_UID' => $application->APP_UID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_INDEX' => 1,
        ]);
        factory(Delegation::class)->create([
            'USR_UID' => $user->USR_UID,
            'PRO_UID' => $process->PRO_UID,
            'APP_UID' => $application->APP_UID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => $appDelegation->DEL_INDEX
        ]);
        factory(Route::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'ROU_NEXT_TASK' => $task2->TAS_UID,
            'PRO_UID' => $process->PRO_UID
        ]);

        $step = factory(Step::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $appDelegation->TAS_UID,
            'STEP_POSITION' => 2,
            'STEP_CONDITION' => '1 == 1'
        ]);


        $triggers = factory(Triggers::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'TRI_WEBBOT' => '$a = 0;'
        ]);
        factory(StepTrigger::class)->create([
            'STEP_UID' => -2,
            'TAS_UID' => $task->TAS_UID,
            'TRI_UID' => $triggers->TRI_UID,
            'ST_CONDITION' => '1 == 1',
            'ST_TYPE' => 'BEFORE'
        ]);


        $triggers = factory(Triggers::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'TRI_WEBBOT' => '$b = 0;'
        ]);
        factory(StepTrigger::class)->create([
            'STEP_UID' => -2,
            'TAS_UID' => $task->TAS_UID,
            'TRI_UID' => $triggers->TRI_UID,
            'ST_CONDITION' => '2 == 2',
            'ST_TYPE' => 'AFTER'
        ]);

        $result = [
            'application' => $application,
            'user' => $user,
            'task' => $task,
            'task2' => $task2,
        ];
        return (object) $result;
    }

    /**
     * This test verifies that the derivation of the case has closed the delegation 
     * with single task.
     * @test
     * @covers \Cases::routeCase
     */
    public function it_should_test_a_derivate_case_with_single_task()
    {
        $result = $this->prepareDerivationData();
        $application = $result->application;
        $user = $result->user;
        $task = $result->task;
        $task2 = $result->task2;

        $processUid = $application->PRO_UID;
        $application = $application->APP_UID;
        $postForm = [
            'ROU_TYPE' => 'SEQUENTIAL',
            'TASKS' => [
                1 => [
                    'TAS_UID' => $task->TAS_UID,
                    'USR_UID' => $user->USR_UID,
                    'TAS_ASSIGN_TYPE' => "BALANCED",
                    'TAS_DEF_PROC_CODE' => "",
                    'DEL_PRIORITY' => "",
                    'TAS_PARENT' => "",
                    'ROU_CONDITION' => "",
                    'SOURCE_UID' => $task->TAS_UID,
                ]
            ]
        ];
        $status = 'TO_DO';
        $flagGmail = true;
        $tasUid = $task->TAS_UID;
        $index = '1';
        $userLogged = $user->USR_UID;

        $cases = new Cases();
        $cases->routeCase($processUid, $application, $postForm, $status, $flagGmail, $tasUid, $index, $userLogged);

        $result = Delegation::where('APP_UID', '=', $application)->where('DEL_INDEX', '=', $index)->first();

        $this->assertEquals('CLOSED', $result->DEL_THREAD_STATUS);
    }

    /**
     * This test verifies that the derivation of the case has closed the delegation 
     * with multiple task.
     * @test
     * @covers \Cases::routeCase
     */
    public function it_should_test_a_derivate_case_with_multiple_task()
    {
        $result = $this->prepareDerivationData();
        $application = $result->application;
        $user = $result->user;
        $task = $result->task;
        $task2 = $result->task2;

        $processUid = $application->PRO_UID;
        $appUid = $application->APP_UID;
        $postForm = [
            'ROU_TYPE' => 'SEQUENTIAL',
            'TASKS' => [
                1 => [
                    'TAS_UID' => $task->TAS_UID,
                    'USR_UID' => $user->USR_UID,
                    'TAS_ASSIGN_TYPE' => "BALANCED",
                    'TAS_DEF_PROC_CODE' => "",
                    'DEL_PRIORITY' => "",
                    'TAS_PARENT' => "",
                    'ROU_CONDITION' => "",
                    'SOURCE_UID' => $task->TAS_UID,
                ],
                2 => [
                    'TAS_UID' => $task2->TAS_UID,
                    'USR_UID' => $user->USR_UID,
                    'TAS_ASSIGN_TYPE' => "BALANCED",
                    'TAS_DEF_PROC_CODE' => "",
                    'DEL_PRIORITY' => "",
                    'TAS_PARENT' => "",
                    'ROU_CONDITION' => "",
                    'SOURCE_UID' => $task2->TAS_UID,
                ]
            ]
        ];
        $status = 'TO_DO';
        $flagGmail = true;
        $tasUid = $task->TAS_UID;
        $index = '1';
        $userLogged = $user->USR_UID;

        $cases = new Cases();
        $cases->routeCase($processUid, $appUid, $postForm, $status, $flagGmail, $tasUid, $index, $userLogged);

        $result = Delegation::where('APP_UID', '=', $appUid)->where('DEL_INDEX', '=', $index)->first();

        $this->assertEquals('CLOSED', $result->DEL_THREAD_STATUS);
    }

    /**
     * This test verifies that the 'CasesDispath' job is in the queue.
     * @test
     * @covers \Cases::routeCase
     */
    public function this_should_add_a_cases_dispath_job_to_the_queue()
    {
        $result = $this->prepareDerivationData();
        $application = $result->application;
        $user = $result->user;
        $task = $result->task;
        $task2 = $result->task2;

        $_SESSION['PROCESS'] = $application->PRO_UID;
        $_SESSION['APPLICATION'] = $application->APP_UID;
        $_POST['form'] = [
            'ROU_TYPE' => 'SEQUENTIAL',
            'TASKS' => [
                1 => [
                    'TAS_UID' => $task->TAS_UID,
                    'USR_UID' => $user->USR_UID,
                    'TAS_ASSIGN_TYPE' => "BALANCED",
                    'TAS_DEF_PROC_CODE' => "",
                    'DEL_PRIORITY' => "",
                    'TAS_PARENT' => "",
                    'ROU_CONDITION' => "",
                    'SOURCE_UID' => $task->TAS_UID,
                ],
                2 => [
                    'TAS_UID' => $task2->TAS_UID,
                    'USR_UID' => $user->USR_UID,
                    'TAS_ASSIGN_TYPE' => "BALANCED",
                    'TAS_DEF_PROC_CODE' => "",
                    'DEL_PRIORITY' => "",
                    'TAS_PARENT' => "",
                    'ROU_CONDITION' => "",
                    'SOURCE_UID' => $task2->TAS_UID,
                ]
            ]
        ];
        $status = 'TO_DO';
        $flagGmail = true;
        $_SESSION['TASK'] = $task->TAS_UID;
        $_SESSION["INDEX"] = '1';
        $_SESSION['USER_LOGGED'] = $user->USR_UID;

        global $RBAC;
        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);

        Queue::fake();
        Queue::assertNothingPushed();

        require_once PATH_METHODS . 'cases/cases_Derivate.php';

        Queue::assertPushed(RouteCase::class);
    }

    /**
     * This test verifies if ABE is completed.
     * @test
     * @covers Cases::routeCaseActionByEmail
     */
    public function it_should_verify_if_abe_is_completed()
    {
        $user = User::where('USR_ID', '=', 1)->first();

        $process = factory(Process::class)->create([
            'PRO_CREATE_USER' => $user->USR_UID
        ]);
        $dynaform = factory(Dynaform::class)->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $inpuDocument = factory(InputDocument::class)->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'BALANCED',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        $task2 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'BALANCED',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);

        $application = factory(Application::class)->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $delegation1 = factory(Delegation::class)->create([
            'USR_UID' => $user->USR_UID,
            'PRO_UID' => $process->PRO_UID,
            'APP_UID' => $application->APP_UID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_INDEX' => 1,
        ]);
        factory(Delegation::class)->create([
            'USR_UID' => $user->USR_UID,
            'PRO_UID' => $process->PRO_UID,
            'APP_UID' => $application->APP_UID,
            'TAS_UID' => $task2->TAS_UID,
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => $delegation1->DEL_INDEX
        ]);
        factory(Route::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'ROU_NEXT_TASK' => $task2->TAS_UID,
            'PRO_UID' => $process->PRO_UID
        ]);

        $emailServer = factory(EmailServerModel::class)->create();
        $abeConfiguration = factory(AbeConfiguration::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'DYN_UID' => $dynaform->DYN_UID,
            'TAS_UID' => $task2->TAS_UID,
            'ABE_EMAIL_SERVER_UID' => $emailServer->MESS_UID,
            'ABE_TYPE' => 'LINK',
            'ABE_CASE_NOTE_IN_RESPONSE' => 1,
        ]);
        $abeRequest = factory(AbeRequest::class)->create([
            'ABE_UID' => $abeConfiguration->ABE_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $delegation1->DEL_INDEX,
        ]);
        if (!defined('PATH_DOCUMENT')) {
            define('PATH_DOCUMENT', PATH_DB . config('system.workspace') . PATH_SEP . 'files' . PATH_SEP);
        }


        $appUid = $delegation1->APP_UID;
        $delIndex = $delegation1->DEL_INDEX;
        $aber = $abeRequest->ABE_REQ_UID;
        $dynUid = $dynaform->DYN_UID;
        $forms = [];
        $remoteAddr = '127.0.0.1';
        $files = [
            'form' => [
                'name' => ['test'],
                'type' => ['test'],
                'size' => ['1000'],
                'tmp_name' => [tempnam(sys_get_temp_dir(), 'test')],
                'error' => [''],
            ]
        ];

        $cases = new Cases();
        $result = $cases->routeCaseActionByEmail($appUid, $delIndex, $aber, $dynUid, $forms, $remoteAddr, $files);

        //asserts
        $this->assertEquals($aber, $result['ABE_REQ_UID']);
        $this->assertArrayHasKey('ABE_RES_STATUS', $result);
    }

    /**
     * This test verifies if the ABE form has not been completed and hope for an exception.
     * @test
     * @covers Cases::routeCaseActionByEmail
     */
    public function it_should_verify_if_abe_has_not_completed()
    {
        $delegation1 = factory(Delegation::class)->state('closed')->create();
        $abeRequest = factory(AbeRequest::class)->create();
        $dynaform = factory(Dynaform::class)->create([
            'PRO_UID' => $delegation1->PRO_UID
        ]);

        $appUid = $delegation1->APP_UID;
        $delIndex = $delegation1->DEL_INDEX;
        $aber = $abeRequest->ABE_REQ_UID;
        $dynUid = $dynaform->DYN_UID;
        $forms = [];
        $remoteAddr = '127.0.0.1';
        $files = [];

        //assert exception
        $this->expectException(\Exception::class);
        $cases = new Cases();
        $cases->routeCaseActionByEmail($appUid, $delIndex, $aber, $dynUid, $forms, $remoteAddr, $files);
    }

    /**
     * This test verifies if the case has failed due to any circumstance.
     * @test
     * @covers Cases::routeCaseActionByEmail
     */
    public function it_should_test_an_exception_if_the_case_throws_an_incorrect_state()
    {
        $delegation1 = factory(Delegation::class)->create();
        $abeRequest = factory(AbeRequest::class)->create();
        $dynaform = factory(Dynaform::class)->create([
            'PRO_UID' => $delegation1->PRO_UID
        ]);

        $appUid = $delegation1->APP_UID;
        $delIndex = $delegation1->DEL_INDEX;
        $aber = $abeRequest->ABE_REQ_UID;
        $dynUid = $dynaform->DYN_UID;
        $forms = [];
        $remoteAddr = '127.0.0.1';
        $files = [];

        //assert exception
        $this->expectException(\Exception::class);
        $cases = new Cases();
        $cases->routeCaseActionByEmail($appUid, $delIndex, $aber, $dynUid, $forms, $remoteAddr, $files);
    }
}
