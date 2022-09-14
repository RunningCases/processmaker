<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Services\Api;

use Luracast\Restler\Data\ApiMethodInfo;
use Luracast\Restler\Defaults;
use Luracast\Restler\HumanReadableCache;
use Maveriks\Extension\Restler;
use ProcessMaker\BusinessModel\Cases\Unassigned;
use ProcessMaker\Model\AppDelay;
use ProcessMaker\Model\Application as ApplicationModel;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use ProcessMaker\Model\User;
use ProcessMaker\Services\Api\Metrics;
use ReflectionClass;
use Tests\TestCase;
use Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases\DraftTest;
use Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases\InboxTest;
use Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases\PausedTest;
use Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases\UnassignedTest;

/**
 * Class MetricsTest
 *
 * @coversDefaultClass @covers \ProcessMaker\Services\Api\Metrics
 */
class MetricsTest extends TestCase
{
    /**
     * Method set up.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->truncateNonInitialModels();
    }
    /**
     * Initialize Rest API.
     * @param string $userUid
     * @return Restler
     */
    private function initializeRestApi(string $userUid)
    {
        //server
        $reflection = new ReflectionClass('\ProcessMaker\Services\OAuth2\Server');

        $reflectionPropertyUserId = $reflection->getProperty('userId');
        $reflectionPropertyUserId->setAccessible(true);
        $reflectionPropertyUserId->setValue($userUid);

        $reflectionPropertyDSN = $reflection->getProperty('dsn');
        $reflectionPropertyDSN->setAccessible(true);
        $reflectionPropertyDSN->setValue('mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'));

        $reflectionPropertyUserName = $reflection->getProperty('dbUser');
        $reflectionPropertyUserName->setAccessible(true);
        $reflectionPropertyUserName->setValue(env('DB_USERNAME'));

        $reflectionPropertyPassword = $reflection->getProperty('dbPassword');
        $reflectionPropertyPassword->setAccessible(true);
        $reflectionPropertyPassword->setValue(env('DB_PASSWORD'));

        //application
        Defaults::$cacheDirectory = PATH_DB . config('system.workspace') . PATH_SEP;
        HumanReadableCache::$cacheDir = PATH_DB . config('system.workspace') . PATH_SEP;

        $rest = new Restler(true);
        $rest->setFlagMultipart(false);
        $rest->setAPIVersion('1.0');
        $rest->addAuthenticationClass('ProcessMaker\\Services\\OAuth2\\Server', '');
        $rest->addAuthenticationClass('ProcessMaker\\Policies\\AccessControl');
        $rest->addAuthenticationClass('ProcessMaker\\Policies\\ControlUnderUpdating');

        $rest->apiMethodInfo = new ApiMethodInfo();
        return $rest;
    }

    /**
     * Tests the getCountersList method with empty lists
     * 
     * @test
     */
    public function it_tests_get_counters_list_method_empty_lists()
    {
        $user = \ProcessMaker\Model\User::factory()->create();
        $this->initializeRestApi($user->USR_UID);

        $metrics = new Metrics();
        $res = $metrics->getCountersList();

        $this->assertEquals(0, $res[0]['Total']);
        $this->assertEquals(0, $res[1]['Total']);
        $this->assertEquals(0, $res[2]['Total']);
        $this->assertEquals(0, $res[3]['Total']);
    }

    /**
     * Tests the getCountersList method
     * 
     * @test
     */
    public function it_tests_get_counters_list_method_inbox()
    {
        $inbox = new InboxTest();
        $user = $inbox->createMultipleInbox(10);
        $this->initializeRestApi($user->USR_UID);
        $metrics = new Metrics();
        $res = $metrics->getCountersList();
        $this->assertEquals(10, $res[0]['Total']);
    }

    /**
     * Tests the getCountersList method
     * 
     * @test
     */
    public function it_tests_get_counters_list_method_draft()
    {
        $draft = new DraftTest();
        $user = $draft->createManyDraft(10);
        $this->initializeRestApi($user->USR_UID);
        $metrics = new Metrics();
        $res = $metrics->getCountersList();
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getCountersList method
     * 
     * @test
     */
    public function it_tests_get_counters_list_method_paused()
    {
        $paused = new PausedTest();
        $user = $paused->createMultiplePaused(5);
        $this->initializeRestApi($user->USR_UID);
        $metrics = new Metrics();
        $res = $metrics->getCountersList();
        $this->assertEquals(5, $res[2]['Total']);
    }

    /**
     * Tests the getCountersList method
     * 
     * @test
     */
    public function it_tests_get_counters_list_method_unassigned()
    {
        $unassignedTest = new UnassignedTest();
        $cases = $unassignedTest->createMultipleUnassigned(3);
        $unassigned = new Unassigned();
        $unassigned->setUserId($cases['taskUser']->USR_ID);
        $unassigned->setUserUid($cases['taskUser']->USR_UID);
        $this->initializeRestApi($cases['taskUser']->USR_UID);
        $metrics = new Metrics();
        $res = $metrics->getCountersList();
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getProcessTotalCases method with inbox
     * 
     * @test
     */
    public function it_tests_get_process_total_cases_inbox()
    {
        Delegation::factory()->foreign_keys()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
        ]);
        $metrics = new Metrics();
        $res = $metrics->getProcessTotalCases('inbox');
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getProcessTotalCases method with draft
     * 
     * @test
     */
    public function it_tests_get_process_total_cases_draft()
    {
        $application = ApplicationModel::factory()->draft()->create();
        Delegation::factory()->foreign_keys()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application->APP_INIT_USER,
            'USR_ID' => $application->APP_INIT_USER_ID,
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
        ]);
        $metrics = new Metrics();
        $res = $metrics->getProcessTotalCases('draft');
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getProcessTotalCases method with paused
     * 
     * @test
     */
    public function it_tests_get_process_total_cases_paused()
    {
        $process1 = Process::factory()->create(
            ['PRO_CATEGORY' => '1']
        );
        $process2 = Process::factory()->create(
            ['PRO_CATEGORY' => '2']
        );
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);
        $application1 = ApplicationModel::factory()->create();
        $application2 = ApplicationModel::factory()->create();
        Delegation::factory()->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1
        ]);
        $delegation1 = Delegation::factory()->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2
        ]);
        Delegation::factory()->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1
        ]);
        $delegation2 = Delegation::factory()->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2
        ]);
        AppDelay::factory(5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        AppDelay::factory(5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation2->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation2->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        $metrics = new Metrics();
        $res = $metrics->getProcessTotalCases('paused');
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getProcessTotalCases method with unassigned
     * 
     * @test
     */
    public function it_tests_get_process_total_cases_unassigned()
    {
        $user = \ProcessMaker\Model\User::factory()->create();
        $process = Process::factory()->create();
        $application = ApplicationModel::factory()->create([
            'APP_STATUS_ID' => 2
        ]);
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
        ]);
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        Delegation::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'PRO_ID' => $process->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => date('Y-m-d H:i:s', strtotime("-1 year"))
        ]);
        $metrics = new Metrics();
        $res = $metrics->getProcessTotalCases('unassigned');
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getTotalCasesByRange method with inbox
     * 
     * @test
     */
    public function it_tests_get_total_cases_by_range_inbox()
    {
        Delegation::factory()->foreign_keys()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
        ]);
        $metrics = new Metrics();
        $res = $metrics->getTotalCasesByRange('inbox');
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getTotalCasesByRange method with draft
     * 
     * @test
     */
    public function it_tests_get_total_cases_by_range_draft()
    {
        $application = ApplicationModel::factory()->draft()->create();
        Delegation::factory()->foreign_keys()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application->APP_INIT_USER,
            'USR_ID' => $application->APP_INIT_USER_ID,
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
        ]);
        $metrics = new Metrics();
        $res = $metrics->getTotalCasesByRange('draft');
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getTotalCasesByRange method with paused
     * 
     * @test
     */
    public function it_tests_get_total_cases_by_range_paused()
    {
        $process1 = Process::factory()->create(
            ['PRO_CATEGORY' => '1']
        );
        $process2 = Process::factory()->create(
            ['PRO_CATEGORY' => '2']
        );
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);
        $application1 = ApplicationModel::factory()->create();
        $application2 = ApplicationModel::factory()->create();
        Delegation::factory()->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1
        ]);
        $delegation1 = Delegation::factory()->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2
        ]);
        Delegation::factory()->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1
        ]);
        $delegation2 = Delegation::factory()->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2
        ]);
        AppDelay::factory(5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        AppDelay::factory(5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation2->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation2->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        $metrics = new Metrics();
        $res = $metrics->getTotalCasesByRange('paused');
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getTotalCasesByRange method with unassigned
     * 
     * @test
     */
    public function it_tests_get_total_cases_by_range_unassigned()
    {
        $user = \ProcessMaker\Model\User::factory()->create();
        $process = Process::factory()->create();
        $application = ApplicationModel::factory()->create([
            'APP_STATUS_ID' => 2
        ]);
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
        ]);
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        Delegation::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'PRO_ID' => $process->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => date('Y-m-d H:i:s', strtotime("-1 year"))
        ]);
        $metrics = new Metrics();
        $res = $metrics->getTotalCasesByRange('unassigned');
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getCasesRiskByProcess method with inbox
     * 
     * @test
     */
    public function it_tests_get_cases_risk_by_process_inbox()
    {
        $process = Process::factory()->create();
        $delegation = Delegation::factory()->foreign_keys()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
            'PRO_ID' => $process->PRO_ID,
            'DEL_RISK_DATE' => date('Y-m-d H:i:s'),
            'DEL_TASK_DUE_DATE' => date('Y-m-d H:i:s', strtotime("+1 hour"))
        ]);
        $metrics = new Metrics();
        $res = $metrics->getCasesRiskByProcess('inbox', $delegation->PRO_ID, null, null, 'AT_RISK');
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getCasesRiskByProcess method with draft
     * 
     * @test
     */
    public function it_tests_get_cases_risk_by_process_draft()
    {
        $process = Process::factory()->create();
        $application = ApplicationModel::factory()->draft()->create();
        $delegation = Delegation::factory()->foreign_keys()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application->APP_INIT_USER,
            'USR_ID' => $application->APP_INIT_USER_ID,
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'PRO_ID' => $process->PRO_ID,
            'DEL_RISK_DATE' => date('Y-m-d H:i:s'),
            'DEL_TASK_DUE_DATE' => date('Y-m-d H:i:s', strtotime("+1 hour"))
        ]);
        $metrics = new Metrics();
        $res = $metrics->getCasesRiskByProcess('draft', $delegation->PRO_ID, null, null, 'AT_RISK');
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getCasesRiskByProcess method with paused
     * 
     * @test
     */
    public function it_tests_get_cases_risk_by_process_paused()
    {
        $process1 = Process::factory()->create(
            ['PRO_CATEGORY' => '1']
        );
        $process2 = Process::factory()->create(
            ['PRO_CATEGORY' => '2']
        );
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);
        $application1 = ApplicationModel::factory()->create();
        $application2 = ApplicationModel::factory()->create();
        Delegation::factory()->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1,
            'DEL_RISK_DATE' => date('Y-m-d H:i:s'),
            'DEL_TASK_DUE_DATE' => date('Y-m-d H:i:s', strtotime("+1 hour"))
        ]);
        $delegation1 = Delegation::factory()->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
            'DEL_RISK_DATE' => date('Y-m-d H:i:s'),
            'DEL_TASK_DUE_DATE' => date('Y-m-d H:i:s', strtotime("+1 hour"))
        ]);
        Delegation::factory()->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1,
            'DEL_RISK_DATE' => date('Y-m-d H:i:s'),
            'DEL_TASK_DUE_DATE' => date('Y-m-d H:i:s', strtotime("+1 hour"))
        ]);
        $delegation2 = Delegation::factory()->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
            'DEL_RISK_DATE' => date('Y-m-d H:i:s'),
            'DEL_TASK_DUE_DATE' => date('Y-m-d H:i:s', strtotime("+1 hour"))
        ]);
        AppDelay::factory(5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        AppDelay::factory(5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation2->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation2->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        $metrics = new Metrics();
        $res = $metrics->getCasesRiskByProcess('paused', $delegation1->PRO_ID, null, null, 'AT_RISK');
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getCasesRiskByProcess method with unassigned
     * 
     * @test
     */
    public function it_tests_get_cases_risk_by_process_unassigned()
    {
        $user = \ProcessMaker\Model\User::factory()->create();
        $process = Process::factory()->create();
        $application = ApplicationModel::factory()->create([
            'APP_STATUS_ID' => 2
        ]);
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
        ]);
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        $delegation = Delegation::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'PRO_ID' => $process->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => date('Y-m-d H:i:s', strtotime("-1 year")),
            'DEL_RISK_DATE' => date('Y-m-d H:i:s'),
            'DEL_TASK_DUE_DATE' => date('Y-m-d H:i:s', strtotime("+1 hour"))
        ]);
        unset($RBAC);
        $metrics = new Metrics();
        $res = $metrics->getCasesRiskByProcess('unassigned', $delegation->PRO_ID, null, null, 'AT_RISK');
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the getProcessTotalCases method with exception
     * 
     * @test
     */
    public function it_tests_get_process_total_cases_exception()
    {
        Delegation::factory()->foreign_keys()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
        ]);
        $metrics = new Metrics();
        $this->expectException('Luracast\Restler\RestException');
        $metrics->getProcessTotalCases(12, 123, "asda");
    }

    /**
     * Tests the getTotalCasesByRange method with exception
     * 
     * @test
     */
    public function it_tests_get_total_cases_by_range_exception()
    {
        Delegation::factory()->foreign_keys()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
        ]);
        $metrics = new Metrics();
        $this->expectException('Luracast\Restler\RestException');
        $metrics->getTotalCasesByRange(12, 123, "asda");
    }

    /**
     * Tests the getCasesRiskByProcess method with exception
     * 
     * @test
     */
    public function it_tests_get_counters_list_exception()
    {
        Delegation::factory()->foreign_keys()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2,
        ]);
        $metrics = new Metrics();
        $this->expectException('Luracast\Restler\RestException');
        $metrics->getCasesRiskByProcess(12, 123, "asda");
    }
}
