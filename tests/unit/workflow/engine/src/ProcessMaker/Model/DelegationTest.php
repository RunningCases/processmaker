<?php

namespace Tests\unit\workflow\src\ProcessMaker\Model;

use DateInterval;
use Datetime;
use G;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\AppAssignSelfServiceValue;
use ProcessMaker\Model\AppAssignSelfServiceValueGroup;
use ProcessMaker\Model\AppDelay;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\AppThread;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\GroupUser;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessCategory;
use ProcessMaker\Model\SubProcess;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class DelegationTest
 *
 * @coversDefaultClass \ProcessMaker\Model\Delegation
 */
class DelegationTest extends TestCase
{
    /**
     * This method is called before the first test of this test class is run.
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::truncateNonInitialModels();
    }

    /**
     * Set up function.
     */
    public function setUp(): void
    {
        parent::setUp();
        Delegation::truncate();
    }

    /**
     * Tear down function.
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test belongs to APP_UID
     *
     * @covers \ProcessMaker\Model\Delegation::application()
     * @test
     */
    public function it_has_an_application()
    {
        $delegation = Delegation::factory()->create([
            'APP_UID' => function () {
                return Application::factory()->create()->APP_UID;
            }
        ]);
        $this->assertInstanceOf(Application::class, $delegation->application);
    }

    /**
     * Test belongs to USR_ID
     *
     * @covers \ProcessMaker\Model\Delegation::user()
     * @test
     */
    public function it_has_an_user()
    {
        $delegation = Delegation::factory()->create([
            'USR_ID' => function () {
                return User::factory()->create()->USR_ID;
            }
        ]);
        $this->assertInstanceOf(User::class, $delegation->user);
    }

    /**
     * Test belongs to TAS_ID
     *
     * @covers \ProcessMaker\Model\Delegation::task()
     * @test
     */
    public function it_has_a_task()
    {
        $delegation = Delegation::factory()->create([
            'TAS_ID' => function () {
                return Task::factory()->create()->TAS_ID;
            }
        ]);
        $this->assertInstanceOf(Task::class, $delegation->task);
    }

    /**
     * Test belongs to PRO_ID
     *
     * @covers \ProcessMaker\Model\Delegation::process()
     * @test
     */
    public function it_has_a_process()
    {
        $delegation = Delegation::factory()->create([
            'PRO_ID' => function () {
                return Process::factory()->create()->PRO_ID;
            }
        ]);
        $this->assertInstanceOf(Process::class, $delegation->process);
    }

    /**
     * This test scopePriority
     *
     * @covers \ProcessMaker\Model\Delegation::scopePriority()
     * @test
     */
    public function it_return_scope_priority()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->priority($table->DEL_PRIORITY)->get());
    }

    /**
     * This test scopePriorities
     *
     * @covers \ProcessMaker\Model\Delegation::scopePriorities()
     * @test
     */
    public function it_return_scope_priorities()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->priorities([$table->DEL_PRIORITY])->get());
    }

    /**
     * This test scopeThreadOpen
     *
     * @covers \ProcessMaker\Model\Delegation::scopeThreadOpen()
     * @test
     */
    public function it_return_scope_thread_open()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->threadOpen()->get());
    }

    /**
     * This test scopeThreadIdOpen
     *
     * @covers \ProcessMaker\Model\Delegation::scopeThreadIdOpen()
     * @test
     */
    public function it_return_scope_thread_id_open()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->threadIdOpen()->get());
    }

    /**
     * This test scopeThreadPause
     *
     * @covers \ProcessMaker\Model\Delegation::scopeThreadPause()
     * @test
     */
    public function it_return_scope_thread_pause()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(0, $table->threadPause()->get());
    }
    /**
     * This test scopeOpenAndPause
     *
     * @covers \ProcessMaker\Model\Delegation::scopeOpenAndPause()
     * @test
     */
    public function it_return_scope_thread_open_and_pause()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->openAndPause()->get());
    }

    /**
     * This test scopeCaseStarted
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCaseStarted()
     * @test
     */
    public function it_return_scope_case_started()
    {
        $table = Delegation::factory()->first_thread()->create();
        $this->assertCount(1, $table->caseStarted($table->DEL_INDEX)->get());
    }

    /**
     * This test scopeCasesInProgress
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCasesInProgress()
     * @test
     */
    public function it_return_scope_case_in_progress()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->joinApplication()->casesInProgress([2])->get());
    }

    /**
     * This test scopeCasesDone
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCasesDone()
     * @test
     */
    public function it_return_scope_case_done()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->joinApplication()->casesDone([2])->get());
    }

    /**
     * This test scopeIndex
     *
     * @covers \ProcessMaker\Model\Delegation::scopeIndex()
     * @test
     */
    public function it_return_scope_index()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->index($table->DEL_INDEX)->get());
    }

    /**
     * This test scopeCaseTodo
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCaseTodo()
     * @test
     */
    public function it_return_scope_case_to_do()
    {
        $application = Application::factory()->todo()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $this->assertCount(1, $table->joinApplication()->caseTodo()->get());
    }

    /**
     * This test scopeCaseCompleted
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCaseCompleted()
     * @test
     */
    public function it_return_scope_case_completed()
    {
        $application = Application::factory()->completed()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $this->assertCount(1, $table->joinApplication()->caseCompleted()->get());
    }

    /**
     * This test scopeCaseCanceled
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCaseCanceled()
     * @test
     */
    public function it_return_scope_case_canceled()
    {
        $application = Application::factory()->canceled()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $this->assertCount(1, $table->joinApplication()->caseCanceled()->get());
    }

    /**
     * This test scopeStatus
     *
     * @covers \ProcessMaker\Model\Delegation::scopeStatus()
     * @test
     */
    public function it_return_scope_status()
    {
        $application = Application::factory()->todo()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $this->assertCount(1, $table->joinApplication()->status($application->APP_STATUS_ID)->get());
    }

    /**
     * This test scopeStatusIds
     *
     * @covers \ProcessMaker\Model\Delegation::scopeStatusIds()
     * @test
     */
    public function it_return_scope_status_ids()
    {
        $application = Application::factory()->todo()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $this->assertCount(1, $table->joinApplication()->statusIds([$application->APP_STATUS_ID])->get());
    }

    /**
     * This test scopeStartDateFrom
     *
     * @covers \ProcessMaker\Model\Delegation::scopeStartDateFrom()
     * @test
     */
    public function it_return_scope_start_date_from()
    {
        $application = Application::factory()->todo()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $this->assertCount(1, $table->joinApplication()->startDateFrom($application->APP_CREATE_DATE->format("Y-m-d H:i:s"))->get());
    }

    /**
     * This test scopeStartDateTo
     *
     * @covers \ProcessMaker\Model\Delegation::scopeStartDateTo()
     * @test
     */
    public function it_return_scope_start_date_to()
    {
        $application = Application::factory()->todo()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $this->assertCount(1, $table->joinApplication()->startDateto($application->APP_CREATE_DATE->format("Y-m-d H:i:s"))->get());
    }

    /**
     * This test scopeFinishCaseFrom
     *
     * @covers \ProcessMaker\Model\Delegation::scopeFinishCaseFrom()
     * @test
     */
    public function it_return_scope_finish_case_date_from()
    {
        $application = Application::factory()->todo()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $this->assertCount(1, $table->joinApplication()->finishCaseFrom($application->APP_FINISH_DATE->format("Y-m-d H:i:s"))->get());
    }

    /**
     * This test scopeFinishCaseTo
     *
     * @covers \ProcessMaker\Model\Delegation::scopeFinishCaseTo()
     * @test
     */
    public function it_return_scope_finish_case_date_to()
    {
        $application = Application::factory()->todo()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $this->assertCount(1, $table->joinApplication()->finishCaseTo($application->APP_FINISH_DATE->format("Y-m-d H:i:s"))->get());
    }

    /**
     * This test scopeDelegateDateFrom
     *
     * @covers \ProcessMaker\Model\Delegation::scopeDelegateDateFrom()
     * @test
     */
    public function it_return_scope_delegate_date_from()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->delegateDateFrom($table->DEL_DELEGATE_DATE->format("Y-m-d H:i:s"))->get());
    }

    /**
     * This test scopeDelegateDateTo
     *
     * @covers \ProcessMaker\Model\Delegation::scopeDelegateDateTo()
     * @test
     */
    public function it_return_scope_delegate_date_to()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->delegateDateTo($table->DEL_DELEGATE_DATE->format("Y-m-d H:i:s"))->get());
    }

    /**
     * This test scopeFinishDateFrom
     *
     * @covers \ProcessMaker\Model\Delegation::scopeFinishDateFrom()
     * @test
     */
    public function it_return_scope_finish_date_from()
    {
        $table = Delegation::factory()->closed()->create();
        $this->assertCount(1, $table->finishDateFrom($table->DEL_FINISH_DATE)->get());
    }

    /**
     * This test scopeFinishDateTo
     *
     * @covers \ProcessMaker\Model\Delegation::scopeFinishDateTo()
     * @test
     */
    public function it_return_scope_finish_date_to()
    {
        $table = Delegation::factory()->closed()->create();
        $this->assertCount(1, $table->finishDateTo($table->DEL_FINISH_DATE)->get());
    }

    /**
     * This test scopeDueFrom
     *
     * @covers \ProcessMaker\Model\Delegation::scopeDueFrom()
     * @test
     */
    public function it_return_scope_due_date_from()
    {
        $table = Delegation::factory()->closed()->create();
        $this->assertCount(1, $table->dueFrom($table->DEL_TASK_DUE_DATE)->get());
    }

    /**
     * This test scopeDueTo
     *
     * @covers \ProcessMaker\Model\Delegation::scopeDueTo()
     * @test
     */
    public function it_return_scope_due_date_to()
    {
        $table = Delegation::factory()->closed()->create();
        $this->assertCount(1, $table->dueTo($table->DEL_TASK_DUE_DATE)->get());
    }

    /**
     * This test scopeOnTime
     *
     * @covers \ProcessMaker\Model\Delegation::scopeOnTime()
     * @test
     */
    public function it_return_scope_on_time()
    {
        $table = Delegation::factory()->closed()->create();
        $this->assertCount(1, $table->onTime($table->DEL_DELEGATE_DATE)->get());
    }

    /**
     * This test scopeAtRisk
     *
     * @covers \ProcessMaker\Model\Delegation::scopeAtRisk()
     * @test
     */
    public function it_return_scope_at_risk()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff2Days = new DateInterval('P2D');

        $table = Delegation::factory()->create([
            'DEL_THREAD_STATUS' => 'CLOSED',
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $currentDate,
            'DEL_TASK_DUE_DATE' => $date->add($diff2Days)
        ]);
        $this->assertCount(1, $table->atRisk($table->DEL_DELEGATE_DATE)->get());
    }

    /**
     * This test scopeOverdue
     *
     * @covers \ProcessMaker\Model\Delegation::scopeOverdue()
     * @test
     */
    public function it_return_scope_overdue()
    {
        $date = new DateTime('now');
        $currentDate = $date->format('Y-m-d H:i:s');
        $diff2Days = new DateInterval('P2D');

        $table = Delegation::factory()->create([
            'DEL_THREAD_STATUS' => 'CLOSED',
            'DEL_DELEGATE_DATE' => $currentDate,
            'DEL_RISK_DATE' => $currentDate,
            'DEL_TASK_DUE_DATE' => $date->sub($diff2Days)
        ]);
        $this->assertCount(1, $table->overdue($table->DEL_DELEGATE_DATE)->get());
    }

    /**
     * This test scopeCase
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCase()
     * @test
     */
    public function it_return_scope_case()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->case($table->APP_NUMBER)->get());
    }

    /**
     * This test scopeSpecificCases
     *
     * @covers \ProcessMaker\Model\Delegation::scopeSpecificCases()
     * @test
     */
    public function it_return_scope_specific_cases()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->specificCases([$table->APP_NUMBER])->get());
    }

    /**
     * This test scopeCasesFrom
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCasesFrom()
     * @test
     */
    public function it_return_scope_cases_from()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->casesFrom($table->APP_NUMBER)->get());
    }

    /**
     * This test scopeCasesTo
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCasesTo()
     * @test
     */
    public function it_return_scope_cases_to()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->casesTo($table->APP_NUMBER)->get());
    }

    /**
     * This test scopePositiveCases
     *
     * @covers \ProcessMaker\Model\Delegation::scopePositiveCases()
     * @test
     */
    public function it_return_scope_positive_cases()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->positiveCases()->get());
    }

    /**
     * This test scopeCasesOrRangeOfCases
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCasesOrRangeOfCases()
     * @test
     */
    public function it_return_scope_cases_and_range_of_cases()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $cases = [$table->APP_NUMBER];
        $rangeCases = [$table->APP_NUMBER . '-' . $table->APP_NUMBER];
        $this->assertCount(1, $table->casesOrRangeOfCases($cases, $rangeCases)->get());
    }

    /**
     * This test scopeRangeOfCases
     *
     * @covers \ProcessMaker\Model\Delegation::scopeRangeOfCases()
     * @test
     */
    public function it_return_scope_range_of_cases()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->rangeOfCases([$table->APP_NUMBER . '-' . $table->APP_NUMBER])->get());
    }

    /**
     * This test scopeAppUid
     *
     * @covers \ProcessMaker\Model\Delegation::scopeAppUid()
     * @test
     */
    public function it_return_scope_app_uid()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->appUid($table->APP_UID)->get());
    }

    /**
     * This test scopeLastThread
     *
     * @covers \ProcessMaker\Model\Delegation::scopeLastThread()
     * @test
     */
    public function it_return_scope_last_thread()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->lastThread()->get());
    }

    /**
     * This test scopeSpecificCasesByUid
     *
     * @covers \ProcessMaker\Model\Delegation::scopeSpecificCasesByUid()
     * @test
     */
    public function it_return_scope_specific_cases_uid()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->specificCasesByUid([$table->APP_UID])->get());
    }

    /**
     * This test scopeUserId
     *
     * @covers \ProcessMaker\Model\Delegation::scopeUserId()
     * @test
     */
    public function it_return_scope_user_id()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->userId($table->USR_ID)->get());
    }

    /**
     * This test scopeWithoutUserId
     *
     * @covers \ProcessMaker\Model\Delegation::scopeWithoutUserId()
     * @test
     */
    public function it_return_scope_without_user_id()
    {
        $table = Delegation::factory()->foreign_keys()->create([
            'USR_ID' => 0
        ]);
        $this->assertCount(1, $table->withoutUserId($table->TAS_ID)->get());
    }

    /**
     * This test scopeProcessId
     *
     * @covers \ProcessMaker\Model\Delegation::scopeProcessId()
     * @test
     */
    public function it_return_scope_process_id()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->processId($table->PRO_ID)->get());
    }

    /**
     * This test scopeTask
     *
     * @covers \ProcessMaker\Model\Delegation::scopeTask()
     * @test
     */
    public function it_return_scope_task_id()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->task($table->TAS_ID)->get());
    }

    /**
     * This test scopeTask
     *
     * @covers \ProcessMaker\Model\Delegation::scopeTask()
     * @test
     */
    public function it_return_scope_task()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->task($table->TAS_ID)->get());
    }

    /**
     * This test scopeSpecificTasks
     *
     * @covers \ProcessMaker\Model\Delegation::scopeSpecificTasks()
     * @test
     */
    public function it_return_scope_specific_tasks()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->specificTasks([$table->TAS_ID])->get());
    }

    /**
     * This test scopeTaskAssignType
     *
     * @covers \ProcessMaker\Model\Delegation::scopeTaskAssignType()
     * @test
     */
    public function it_return_scope_assign_type()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->taskAssignType('NORMAL')->get());
    }

    /**
     * This test scopeExcludeTaskTypes
     *
     * @covers \ProcessMaker\Model\Delegation::scopeExcludeTaskTypes()
     * @test
     */
    public function it_return_scope_exclude_tas_types()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertNotEmpty($table->excludeTaskTypes(['ADHOC'])->get());
    }

    /**
     * This test scopeSpecificTaskTypes
     *
     * @covers \ProcessMaker\Model\Delegation::scopeSpecificTaskTypes()
     * @test
     */
    public function it_return_scope_specific_tas_types()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->specificTaskTypes(['NORMAL'])->get());
    }

    /**
     * This test scopeAppStatusId
     *
     * @covers \ProcessMaker\Model\Delegation::scopeAppStatusId()
     * @test
     */
    public function it_return_scope_status_id()
    {
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 2,
            'APP_STATUS' => 'TO_DO'
        ]);
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $this->assertCount(1, $table->appStatusId()->get());
    }

    /**
     * This test scopeProcessInList
     *
     * @covers \ProcessMaker\Model\Delegation::scopeProcessInList()
     * @test
     */
    public function it_return_scope_process_in_list()
    {
        $process = Process::factory()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'PRO_ID' => $process->PRO_ID
        ]);
        $this->assertCount(1, $table->joinProcess()->processInList([$table->PRO_ID])->get());
    }

    /**
     * This test scopeParticipated
     *
     * @covers \ProcessMaker\Model\Delegation::scopeParticipated()
     * @test
     */
    public function it_return_scope_participated()
    {
        $table = Delegation::factory()->foreign_keys()->create();
        $this->assertCount(1, $table->participated($table->USR_ID)->get());
    }

    /**
     * This test scopeCategoryId
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCategoryId()
     * @test
     */
    public function it_return_scope_category()
    {
        $process = Process::factory()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'PRO_ID' => $process->PRO_ID
        ]);
        $this->assertCount(1, $table->joinProcess()->categoryId($process->CATEGORY_ID)->get());
    }

    /**
     * This test scopeJoinCategoryProcess
     *
     * @covers \ProcessMaker\Model\Delegation::scopeJoinCategoryProcess()
     * @test
     */
    public function it_return_scope_join_category_process()
    {
        $category = ProcessCategory::factory()->create();
        $process = Process::factory()->create([
            'PRO_CATEGORY' => $category->CATEGORY_UID
        ]);
        $table = Delegation::factory()->foreign_keys()->create([
            'PRO_ID' => $process->PRO_ID
        ]);
        $this->assertCount(1, $table->joinCategoryProcess($category->CATEGORY_UID)->get());
    }

    /**
     * This test scopeJoinPreviousIndex
     *
     * @covers \ProcessMaker\Model\Delegation::scopeJoinPreviousIndex()
     * @test
     */
    public function it_return_scope_join_previous_index()
    {
        $previous = Delegation::factory()->foreign_keys()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $previous->APP_NUMBER,
            'DEL_INDEX' => $previous->DEL_INDEX + 1,
            'DEL_PREVIOUS' => $previous->DEL_INDEX
        ]);
        $this->assertNotEmpty($table->joinPreviousIndex()->get());
    }

    /**
     * This test scopeJoinProcess
     *
     * @covers \ProcessMaker\Model\Delegation::scopeJoinProcess()
     * @test
     */
    public function it_return_scope_join_process()
    {
        $process = Process::factory()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'PRO_ID' => $process->PRO_ID
        ]);
        $this->assertCount(1, $table->joinProcess()->get());
    }

    /**
     * This test scopeJoinTask
     *
     * @covers \ProcessMaker\Model\Delegation::scopeJoinTask()
     * @test
     */
    public function it_return_scope_join_task()
    {
        $task = Task::factory()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'TAS_ID' => $task->TAS_ID
        ]);
        $this->assertCount(1, $table->joinTask()->get());
    }

    /**
     * This test scopeJoinUser
     *
     * @covers \ProcessMaker\Model\Delegation::scopeJoinUser()
     * @test
     */
    public function it_return_scope_join_user()
    {
        $user = User::factory()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'USR_ID' => $user->USR_ID
        ]);
        $this->assertCount(1, $table->joinUser()->get());
    }

    /**
     * This test scopeJoinApplication
     *
     * @covers \ProcessMaker\Model\Delegation::scopeJoinApplication()
     * @test
     */
    public function it_return_scope_join_application()
    {
        $application = Application::factory()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $this->assertCount(1, $table->joinApplication()->get());
    }

    /**
     * This test scopeJoinAppDelay
     *
     * @covers \ProcessMaker\Model\Delegation::scopeJoinAppDelay()
     * @covers \ProcessMaker\Model\Delegation::scopeJoinAppDelayUsers()
     * @test
     */
    public function it_return_scope_join_app_delay_pause()
    {
        $user = User::factory()->create();
        $delay = AppDelay::factory()->create([
            'APP_TYPE' => 'PAUSE',
            'APP_DISABLE_ACTION_USER' => '0',
            'APP_DELEGATION_USER' => $user->USR_UID,
        ]);
        $table = Delegation::factory()->foreign_keys()->create([
            'USR_ID' => $user->USR_ID,
            'USR_UID' => $user->USR_UID,
            'APP_NUMBER' => $delay->APP_NUMBER,
            'DEL_INDEX' => $delay->APP_DEL_INDEX
        ]);
        $this->assertCount(1, $table->joinAppDelay('PAUSE')->joinAppDelayUsers($user->USR_ID)->get());
    }

    /**
     * This checks to make sure pagination is working properly
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_pages_of_data()
    {
        Delegation::factory(51)->foreign_keys()->create();
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, null, null, null, null, null, null, null, 'APP_NUMBER');
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, null, null, null, null, null, null, null, 'APP_NUMBER');
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, null, null, null, null, null, null, null, 'APP_NUMBER');
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure pagination is working properly
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_pages_of_data_unassigned()
    {
        Delegation::factory(50)->foreign_keys()->create();
        Delegation::factory(1)->foreign_keys()->create([
            'USR_ID' => 0 // A self service delegation
        ]);
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure filter by process is working properly
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_process_of_data()
    {

        $process = Process::factory()->create();
        Delegation::factory(51)->foreign_keys()->create([
            'PRO_ID' => $process->PRO_ID
        ]);
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, $process->PRO_ID);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, $process->PRO_ID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, $process->PRO_ID);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure filter by status is working properly
     * Review status filter by a specific status, such as Draft
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_status_draft_of_data()
    {
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 1,
            'APP_STATUS' => 'DRAFT'
        ]);
        Delegation::factory(51)->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        // Review the filter by status DRAFT
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, null, $application->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, null, $application->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, null, $application->APP_STATUS_ID);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure filter by status is working properly
     * Review status filter by a specific status, such as To Do
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_status_todo_of_data()
    {
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 2,
            'APP_STATUS' => 'TO_DO'
        ]);
        Delegation::factory(51)->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        // Review the filter by status TO_DO
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, null, $application->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, null, $application->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, null, $application->APP_STATUS_ID);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure filter by status is working properly
     * Review status filter by a specific status, such as Completed
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_status_completed_of_data()
    {
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 3,
            'APP_STATUS' => 'COMPLETED',
        ]);
        Delegation::factory(51)->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_LAST_INDEX' => 1
        ]);
        // Review the filter by status COMPLETED
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, null, $application->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, null, $application->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, null, $application->APP_STATUS_ID);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure filter by status is working properly
     * Review status filter by a specific status, such as Cancelled
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_status_cancelled_of_data()
    {
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 4,
            'APP_STATUS' => 'CANCELLED'
        ]);
        Delegation::factory(51)->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_LAST_INDEX' => 1
        ]);
        // Review the filter by status CANCELLED
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, null, $application->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, null, $application->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, null, $application->APP_STATUS_ID);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This ensures searching for a valid user works
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_one_result_for_specified_user()
    {
        // Create our unique user, with a unique username
        $user = User::factory()->create();
        // Create a new delegation, but for this specific user
        Delegation::factory()->foreign_keys()->create([
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID
        ]);
        // Now fetch results, and assume delegation count is 1 and the user points to our user
        $results = Delegation::search($user->USR_ID);
        $this->assertCount(1, $results['data']);
        $this->assertEquals($user->USR_USERNAME, $results['data'][0]['USRCR_USR_USERNAME']);
    }

    /**
     * This ensures searching by case number and review the order
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_search_and_filter_by_app_number()
    {
        for ($x = 1; $x <= 10; $x++) {
            $application = Application::factory()->create();
            Delegation::factory()->foreign_keys()->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'DEL_THREAD_STATUS' => 'OPEN'
            ]);
        }
        // Searching by a existent case number, result ordered by APP_NUMBER, filter by APP_NUMBER in DESC mode
        $results = Delegation::search(
            null,
            0,
            25,
            $application->APP_NUMBER,
            null,
            null,
            'DESC',
            'APP_NUMBER',
            null,
            null,
            null,
            'APP_NUMBER'
        );
        $this->assertCount(1, $results['data']);
        $this->assertEquals($application->APP_NUMBER, $results['data'][0]['APP_NUMBER']);
        // Searching by a existent case number, result ordered by APP_NUMBER, filter by APP_NUMBER in ASC mode
        $results = Delegation::search(
            null,
            0,
            25,
            $application->APP_NUMBER,
            null,
            null,
            'ASC',
            'APP_NUMBER',
            null,
            null,
            null,
            'APP_NUMBER'
        );
        $this->assertCount(1, $results['data']);
        $this->assertEquals($application->APP_NUMBER, $results['data'][0]['APP_NUMBER']);
    }

    /**
     * This ensures searching by case number and review the order
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_search_and_filter_by_app_title()
    {
        $delegations = Delegation::factory(1)->foreign_keys()->create([
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' => 0
        ]);
        $title = $delegations->last()->DEL_TITLE;
        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();

        // Searching by a existent case title, result ordered by APP_NUMBER, filter by APP_NUMBER in DESC mode
        $results = Delegation::search(
            null,
            0,
            10,
            $title,
            null,
            null,
            'DESC',
            'APP_NUMBER',
            null,
            null,
            null,
            'APP_TITLE'
        );
        $this->assertCount(1, $results['data']);
        $this->assertEquals($title, $results['data'][0]['APP_TITLE']);
        // Searching by a existent case title, result ordered by APP_NUMBER, filter by APP_NUMBER in ASC mode
        $results = Delegation::search(
            null,
            0,
            10,
            $title,
            null,
            null,
            'ASC',
            'APP_NUMBER',
            null,
            null,
            null,
            'APP_TITLE'
        );
        $this->assertCount(1, $results['data']);
        $this->assertEquals($title, $results['data'][0]['APP_TITLE']);
    }

    /**
     * This ensures searching by task title and review the page
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_search_and_order_by_task_title()
    {
        Delegation::factory(5)->foreign_keys()->create([
            'TAS_ID' => function () {
                return Task::factory()->create()->TAS_ID;
            }
        ]);
        $task = Task::factory()->create();
        Delegation::factory()->foreign_keys()->create([
            'TAS_ID' => $task->TAS_ID
        ]);
        // Get the order taskTitle in ASC mode
        $results = Delegation::search(
            null,
            0,
            10,
            $task->TAS_TITLE,
            null,
            null,
            'ASC',
            'TAS_TITLE',
            null,
            null,
            null,
            'TAS_TITLE'
        );
        $this->assertCount(1, $results['data']);
        $this->assertEquals($task->TAS_TITLE, $results['data'][0]['APP_TAS_TITLE']);
        $results = Delegation::search(
            null,
            0,
            10,
            null,
            null,
            null,
            'ASC',
            'TAS_TITLE',
            null,
            null,
            null,
            'TAS_TITLE'
        );
        $this->assertGreaterThan($results['data'][0]['APP_TAS_TITLE'], $results['data'][1]['APP_TAS_TITLE']);

        // Get the order taskTitle in DESC mode
        $results = Delegation::search(
            null,
            0,
            10,
            $task->TAS_TITLE,
            null,
            null,
            'DESC',
            'TAS_TITLE',
            null,
            null,
            null,
            'TAS_TITLE'
        );
        $this->assertCount(1, $results['data']);
        $this->assertEquals($task->TAS_TITLE, $results['data'][0]['APP_TAS_TITLE']);
        $results = Delegation::search(
            null,
            0,
            10,
            null,
            null,
            null,
            'DESC',
            'TAS_TITLE',
            null,
            null,
            null,
            'TAS_TITLE'
        );
        $this->assertLessThan($results['data'][0]['APP_TAS_TITLE'], $results['data'][1]['APP_TAS_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by case number APP_NUMBER
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_sort_by_case_id()
    {
        Delegation::factory(2)->foreign_keys()->create();
        // Get first page, the minor case id
        $results = Delegation::search(null, 0, 2, null, null, null, 'ASC', 'APP_NUMBER');
        $this->assertCount(2, $results['data']);
        $this->assertGreaterThan($results['data'][0]['APP_NUMBER'], $results['data'][1]['APP_NUMBER']);
        // Get first page, the major case id
        $results = Delegation::search(null, 0, 2, null, null, null, 'DESC', 'APP_NUMBER');
        $this->assertCount(2, $results['data']);
        $this->assertLessThan($results['data'][0]['APP_NUMBER'], $results['data'][1]['APP_NUMBER']);
    }

    /**
     * This ensures ordering ascending and descending works by case title APP_TITLE
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_sort_by_case_title()
    {
        Delegation::factory(2)->foreign_keys()->create();
        // Get first page, the minor case title
        $results = Delegation::search(null, 0, 2, null, null, null, 'ASC', 'APP_TITLE');
        $this->assertCount(2, $results['data']);
        $this->assertGreaterThanOrEqual($results['data'][0]['APP_TITLE'], $results['data'][1]['APP_TITLE']);
        // Get first page, the major case title
        $results = Delegation::search(null, 0, 2, null, null, null, 'DESC', 'APP_TITLE');
        $this->assertCount(2, $results['data']);
        $this->assertLessThan($results['data'][0]['APP_TITLE'], $results['data'][1]['APP_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by case title APP_PRO_TITLE
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_sort_by_process()
    {
        Delegation::factory(3)->foreign_keys()->create([
            'PRO_ID' => function () {
                return Process::factory()->create()->PRO_ID;
            }
        ]);
        // Get first page, all process ordering ASC
        $results = Delegation::search(null, 0, 3, null, null, null, 'ASC', 'APP_PRO_TITLE');
        $this->assertCount(3, $results['data']);
        $this->assertGreaterThan($results['data'][0]['APP_PRO_TITLE'], $results['data'][1]['APP_PRO_TITLE']);
        // Get first page, all process ordering DESC
        $results = Delegation::search(null, 0, 3, null, null, null, 'DESC', 'APP_PRO_TITLE');
        $this->assertCount(3, $results['data']);
        $this->assertLessThan($results['data'][0]['APP_PRO_TITLE'], $results['data'][1]['APP_PRO_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by task title APP_TAS_TITLE
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_sort_by_task_title()
    {
        Delegation::factory(2)->foreign_keys()->create([
            'TAS_ID' => function () {
                return Task::factory()->create()->TAS_ID;
            }
        ]);
        // Get first page, all titles ordering ASC
        $results = Delegation::search(null, 0, 2, null, null, null, 'ASC', 'APP_TAS_TITLE');
        $this->assertCount(2, $results['data']);
        $this->assertGreaterThan($results['data'][0]['APP_TAS_TITLE'], $results['data'][1]['APP_TAS_TITLE']);
        // Get first page, all titles ordering DESC
        $results = Delegation::search(null, 0, 2, null, null, null, 'DESC', 'APP_TAS_TITLE');
        $this->assertCount(2, $results['data']);
        $this->assertLessThan($results['data'][0]['APP_TAS_TITLE'], $results['data'][1]['APP_TAS_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by current user
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_sort_by_user()
    {
        Delegation::factory(2)->foreign_keys()->create([
            'USR_ID' => function () {
                return User::factory()->create()->USR_ID;
            }
        ]);
        // Get first page, order by User ordering ASC
        $results = Delegation::search(null, 0, 2, null, null, null, 'ASC', 'APP_CURRENT_USER');
        $this->assertCount(2, $results['data']);
        $this->assertGreaterThan($results['data'][0]['APP_CURRENT_USER'], $results['data'][1]['APP_CURRENT_USER']);
        // Get first page, order by User ordering ASC
        $results = Delegation::search(null, 0, 2, null, null, null, 'DESC', 'APP_CURRENT_USER');
        $this->assertCount(2, $results['data']);
        $this->assertLessThan($results['data'][0]['APP_CURRENT_USER'], $results['data'][1]['APP_CURRENT_USER']);
    }

    /**
     * This ensures ordering ordering ascending and descending works by last modified APP_UPDATE_DATE
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_sort_by_last_modified()
    {
        Delegation::factory(2)->foreign_keys()->create([
            'APP_NUMBER' => function () {
                return Application::factory()->create()->APP_NUMBER;
            }
        ]);
        // Get first page, the minor last modified
        $results = Delegation::search(null, 0, 2, null, null, null, 'ASC', 'APP_UPDATE_DATE');
        $this->assertCount(2, $results['data']);
        $this->assertGreaterThan($results['data'][0]['APP_UPDATE_DATE'], $results['data'][1]['APP_UPDATE_DATE']);
        // Get first page, the major last modified
        $results = Delegation::search(null, 0, 2, null, null, null, 'DESC', 'APP_UPDATE_DATE');
        $this->assertCount(2, $results['data']);
        $this->assertLessThan($results['data'][0]['APP_UPDATE_DATE'], $results['data'][1]['APP_UPDATE_DATE']);
    }

    /**
     * This ensures ordering ascending and descending works by due date DEL_TASK_DUE_DATE
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_sort_by_due_date()
    {
        Delegation::factory(10)->foreign_keys()->create();
        // Get first page, the minor due date
        $results = Delegation::search(null, 0, 10, null, null, null, 'ASC', 'DEL_TASK_DUE_DATE');
        $this->assertCount(10, $results['data']);
        $this->assertGreaterThan($results['data'][0]['DEL_TASK_DUE_DATE'], $results['data'][1]['DEL_TASK_DUE_DATE']);
        // Get first page, the major due date
        $results = Delegation::search(null, 0, 10, null, null, null, 'DESC', 'DEL_TASK_DUE_DATE');
        $this->assertCount(10, $results['data']);
        $this->assertLessThan($results['data'][0]['DEL_TASK_DUE_DATE'], $results['data'][1]['DEL_TASK_DUE_DATE']);
    }

    /**
     * This ensures ordering ascending and descending works by status APP_STATUS
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_sort_by_status()
    {
        Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => function () {
                return Application::factory()->create(['APP_STATUS' => 'DRAFT', 'APP_STATUS_ID' => 1])->APP_NUMBER;
            }
        ]);
        Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => function () {
                return Application::factory()->create(['APP_STATUS' => 'TO_DO', 'APP_STATUS_ID' => 2])->APP_NUMBER;
            }
        ]);
        Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => function () {
                return Application::factory()->create([
                    'APP_STATUS' => 'COMPLETED',
                    'APP_STATUS_ID' => 3
                ])->APP_NUMBER;
            }
        ]);
        Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => function () {
                return Application::factory()->create([
                    'APP_STATUS' => 'CANCELLED',
                    'APP_STATUS_ID' => 4
                ])->APP_NUMBER;
            }
        ]);
        // Get first page, the minor status label
        $results = Delegation::search(null, 0, 5, null, null, null, 'ASC', 'APP_STATUS_LABEL');
        $this->assertGreaterThanOrEqual($results['data'][0]['APP_STATUS'], $results['data'][1]['APP_STATUS']);
        // Get first page, the major status label
        $results = Delegation::search(null, 0, 5, null, null, null, 'DESC', 'APP_STATUS_LABEL');
        $this->assertLessThanOrEqual($results['data'][0]['APP_STATUS'], $results['data'][1]['APP_STATUS']);
    }

    /**
     * This checks to make sure filter by category is working properly
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_data_filtered_by_process_category()
    {
        // Dummy Processes
        ProcessCategory::factory(4)->create();
        Process::factory(4)->create([
            'PRO_CATEGORY' => ProcessCategory::all()->random()->CATEGORY_UID
        ]);
        // Dummy Delegations
        Delegation::factory(100)->create([
            'PRO_ID' => Process::all()->random()->PRO_ID
        ]);
        // Process with the category to search
        $category = ProcessCategory::factory()->create();
        $processSearch = Process::factory()->create([
            'PRO_CATEGORY' => $category->CATEGORY_UID
        ]);
        // Delegations to found
        Delegation::factory(51)->foreign_keys()->create([
            'PRO_ID' => $processSearch->PRO_ID
        ]);
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, null, null, null, null, $category->CATEGORY_UID);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, null, null, null, null, $category->CATEGORY_UID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, null, null, null, null, $category->CATEGORY_UID);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This ensure the result is right when you search between two given dates
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_right_data_between_two_dates()
    {
        Delegation::factory(10)->foreign_keys()->create([
            'DEL_DELEGATE_DATE' => '2019-01-02 00:00:00'
        ]);
        Delegation::factory(10)->foreign_keys()->create([
            'DEL_DELEGATE_DATE' => '2019-01-03 00:00:00'
        ]);
        Delegation::factory(10)->foreign_keys()->create([
            'DEL_DELEGATE_DATE' => '2019-01-04 00:00:00'
        ]);
        Delegation::factory(10)->foreign_keys()->create([
            'DEL_DELEGATE_DATE' => '2019-01-05 00:00:00'
        ]);
        $results = Delegation::search(
            null,
            0,
            25,
            null,
            null,
            null,
            null,
            null,
            null,
            '2019-01-02 00:00:00',
            '2019-01-03 00:00:00'
        );
        $this->assertCount(20, $results['data']);
        foreach ($results['data'] as $value) {
            $this->assertGreaterThanOrEqual('2019-01-02 00:00:00', $value['DEL_DELEGATE_DATE']);
            $this->assertLessThanOrEqual('2019-01-03 00:00:00', $value['DEL_DELEGATE_DATE']);
            $this->assertMatchesRegularExpression('(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) ', $value['DEL_DELEGATE_DATE']);
        }
    }

    /**
     * This ensure the result is right when you search from a given date
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_right_data_with_filters_dates_parameter()
    {
        Delegation::factory(10)->foreign_keys()->create([
            'DEL_DELEGATE_DATE' => '2019-01-02 00:00:00'
        ]);
        Delegation::factory(10)->foreign_keys()->create([
            'DEL_DELEGATE_DATE' => '2019-01-03 00:00:00'
        ]);
        Delegation::factory(10)->foreign_keys()->create([
            'DEL_DELEGATE_DATE' => '2019-01-04 00:00:00'
        ]);
        Delegation::factory(10)->foreign_keys()->create([
            'DEL_DELEGATE_DATE' => '2019-01-05 00:00:00'
        ]);
        // Search setting only from
        $results = Delegation::search(
            null,
            0,
            40,
            null,
            null,
            null,
            null,
            null,
            null,
            '2019-01-02 00:00:00'
        );
        $this->assertCount(40, $results['data']);
        foreach ($results['data'] as $value) {
            $this->assertGreaterThanOrEqual('2019-01-02 00:00:00', $value['DEL_DELEGATE_DATE']);
            $this->assertMatchesRegularExpression('(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) ', $value['DEL_DELEGATE_DATE']);
        }
        // Search setting only to
        $results = Delegation::search(
            null,
            0,
            40,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            '2019-01-04 00:00:00'
        );
        foreach ($results['data'] as $value) {
            $this->assertLessThanOrEqual('2019-01-04 00:00:00', $value['DEL_DELEGATE_DATE']);
            $this->assertMatchesRegularExpression('(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) ', $value['DEL_DELEGATE_DATE']);
        }
    }

    /**
     * This ensures return the correct data by parallel task all threads CLOSED
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_empty_when_parallel_tasks_has_threads_closed()
    {
        // Create a process
        $process = Process::factory()->create();
        // Create a task
        $parallelTask = Task::factory()->create();
        // Create a case
        $application = Application::factory()->create();
        // Create the threads for a parallel process
        Delegation::factory(5)->foreign_keys()->create([
            'PRO_ID' => $process->PRO_ID,
            'TAS_ID' => $parallelTask->TAS_ID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'CLOSED'
        ]);
        // Get first page, searching for threads are closed
        $results = Delegation::search(
            null,
            0,
            5,
            $application->APP_NUMBER,
            null,
            null,
            'asc',
            'TAS_TITLE',
            null,
            null,
            null,
            'TAS_TITLE'
        );
        $this->assertCount(0, $results['data']);
    }

    /**
     * This ensures return the correct data by parallel task all threads OPEN
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_data_when_parallel_tasks_has_threads_open()
    {
        // Create a process
        $process = Process::factory()->create();
        // Create a task
        $parallelTask = Task::factory()->create();
        // Create a case
        $application = Application::factory()->create();
        // Create the threads for a parallel process
        Delegation::factory(5)->foreign_keys()->create([
            'PRO_ID' => $process->PRO_ID,
            'TAS_ID' => $parallelTask->TAS_ID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'OPEN'
        ]);
        // Get first page, all the open status
        $results = Delegation::search(null, 0, 5, null, null, null);
        $this->assertCount(5, $results['data']);
        $this->assertEquals('OPEN', $results['data'][rand(0, 4)]['DEL_THREAD_STATUS']);
    }

    /**
     * This ensures return the correct data by sequential
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_data_in_sequential_tasks_with_intermediate_dummy_task()
    {
        // Create a process
        $process = Process::factory()->create();
        // Create a task
        $parallelTask = Task::factory()->create();
        // Create a case
        $application = Application::factory()->create(['APP_STATUS_ID' => 2]);
        // Create the threads for a parallel process closed
        Delegation::factory()->closed()->create([
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_ID' => $parallelTask->TAS_ID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 1
        ]);
        // Create the threads for a parallel process closed
        Delegation::factory()->open()->create([
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_ID' => $parallelTask->TAS_ID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2
        ]);
        // Get first page, searching for threads are open
        $results = Delegation::search(
            null,
            0,
            5,
            $application->APP_NUMBER,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            'APP_NUMBER'
        );

        $this->assertCount(1, $results['data']);
    }

    /**
     * Review when the status is empty
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_status_empty()
    {
        Delegation::factory(5)->foreign_keys()->create([
            'APP_NUMBER' => function () {
                return Application::factory()->create(['APP_STATUS' => ''])->APP_NUMBER;
            }
        ]);
        // Review the filter by status empty
        $results = Delegation::search(null, 0, 5, null, null, null, 'ASC', 'APP_STATUS_LABEL');
        $this->assertEmpty($results['data'][0]['APP_STATUS']);
    }

    /**
     * Review when filter when the process and category does not have a relation
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_empty_when_process_and_category_does_not_have_a_relation()
    {
        // Create a categories
        $category = ProcessCategory::factory(2)->create();
        //Create a process with category
        $processWithCat = Process::factory()->create(['PRO_CATEGORY' => $category[0]->CATEGORY_UID]);
        Delegation::factory()->foreign_keys()->create([
            'PRO_ID' => $processWithCat->PRO_ID
        ]);
        // Create a process without category
        $processWithoutCat = Process::factory()->create(['PRO_CATEGORY' => '']);
        Delegation::factory(5)->foreign_keys()->create([
            'PRO_ID' => $processWithoutCat->PRO_ID
        ]);
        // Search the cases when the process has related to the category and search by another category
        $results = Delegation::search(
            null,
            0,
            25,
            null,
            $processWithCat->PRO_ID,
            null,
            null,
            null,
            $category[1]->CATEGORY_UID
        );
        $this->assertCount(0, $results['data']);
        // Search the cases when the process has related to the category and search by this relation
        $results = Delegation::search(
            null,
            0,
            25,
            null,
            $processWithCat->PRO_ID,
            null,
            null,
            null,
            $category[0]->CATEGORY_UID
        );
        $this->assertCount(1, $results['data']);
        // Search the cases when the process does not have relation with category and search by a category
        $results = Delegation::search(
            null,
            0,
            25,
            null,
            $processWithoutCat->PRO_ID,
            null,
            null,
            null,
            $category[1]->CATEGORY_UID
        );
        $this->assertCount(0, $results['data']);
        // Search the cases when the process does not have relation with category empty
        $results = Delegation::search(
            null,
            0,
            25,
            null,
            $processWithoutCat->PRO_ID,
            null,
            null,
            null,
            ''
        );
        $this->assertCount(5, $results['data']);
    }

    /**
     * Review when filter when the process and category does have a relation
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_data_when_process_and_category_does_have_a_relation()
    {
        //Create a category
        $category = ProcessCategory::factory()->create();
        //Define a process related with he previous category
        $processWithCat = Process::factory()->create([
            'PRO_CATEGORY' => $category->CATEGORY_UID
        ]);
        //Create a delegation related to this process
        Delegation::factory()->create([
            'PRO_ID' => $processWithCat->PRO_ID
        ]);
        //Define a process related with he previous category
        $process = Process::factory()->create([
            'PRO_CATEGORY' => ''
        ]);
        //Create a delegation related to other process
        $delegation = Delegation::factory(5)->create([
            'PRO_ID' => $process->PRO_ID,
        ]);

        $this->assertEquals($process->PRO_ID, $delegation[0]->PRO_ID);
    }

    /**
     * Check if return participation information
     *
     * @covers \ProcessMaker\Model\Delegation::getParticipatedInfo()
     * @test
     */
    public function it_should_return_participation_info()
    {
        // Creating one application with two delegations
        User::factory(100)->create();
        $process = Process::factory()->create();
        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'APP_UID' => G::generateUniqueID()
        ]);
        Delegation::factory()->closed()->create([
            'PRO_UID' => $process->PRO_UID,
            'APP_UID' => $application->APP_UID
        ]);
        Delegation::factory()->open()->create([
            'PRO_UID' => $process->PRO_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 2
        ]);

        // Check the information returned
        $results = Delegation::getParticipatedInfo($application->APP_UID);
        $this->assertEquals('PARTICIPATED', $results['APP_STATUS']);
        $this->assertCount(2, $results['DEL_INDEX']);
        $this->assertEquals($process->PRO_UID, $results['PRO_UID']);
    }

    /**
     * Check if return an empty participation information
     *
     * @covers \ProcessMaker\Model\Delegation::getParticipatedInfo()
     * @test
     */
    public function it_should_return_empty_participation_info()
    {
        // Try to get the participation information from a case that not exists
        $results = Delegation::getParticipatedInfo(G::generateUniqueID());

        // Check the information returned
        $this->assertEmpty($results);
    }

    /**
     * This checks if get the query is working properly in self-service user assigned
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfServiceQuery()
     * @test
     */
    public function it_should_get_query_cases_by_user_with_self_service_user_assigned()
    {
        //Create process
        $process = Process::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a task self service
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in delegation relate to self-service
        Delegation::factory(25)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the self-service query
        $result = Delegation::getSelfServiceQuery($user->USR_UID);
        $this->assertTrue(is_string($result));
    }

    /**
     * This checks if get the query is working properly in self-service-value-based when the variable has a value related
     * with the USR_UID When the value assigned in the variable @@ARRAY_OF_USERS = [USR_UID]
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfServiceQuery()
     * @test
     */
    public function it_should_get_query_cases_by_user_with_self_service_value_based_usr_uid()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a task self service value based
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory(25)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the self-service query
        $result = Delegation::getSelfServiceQuery($user->USR_UID);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $result);
    }

    /**
     * This checks if get the query is working properly in self-service group assigned
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfServiceQuery()
     * @test
     */
    public function it_should_get_query_cases_by_user_with_self_service_group_assigned()
    {
        //Create process
        $process = Process::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        //Create a task self service
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the register in self-service
        Delegation::factory(25)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the self-service query
        $result = Delegation::getSelfServiceQuery($user->USR_UID);
        $this->assertTrue(is_string($result));
    }

    /**
     * This checks if get the query is working properly in self-service-value-based when the variable has a value related
     * with the GRP_UID When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID]
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfServiceQuery()
     * @test
     */
    public function it_should_get_query_cases_by_user_with_self_service_value_based_grp_uid()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a task self service value based
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create a case
        $application = Application::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create([
            'USR_USERNAME' => 'gary',
            'USR_LASTNAME' => 'Gary',
            'USR_FIRSTNAME' => 'Bailey',
        ]);
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $group->GRP_UID,
            'ASSIGNEE_ID' => $group->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory(25)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the self-service query
        $result = Delegation::getSelfServiceQuery($user->USR_UID);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $result);
    }

    /**
     * This checks if get the query is working properly with self-service and self-service-value-based
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfServiceQuery()
     * @test
     */
    public function it_should_get_query_cases_by_user_with_self_service_and_self_service_value_based()
    {
        //Create process
        $process = Process::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        //Create a task self service
        $taskSelfService = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $taskSelfService->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in self service
        Delegation::factory()->create([
            'TAS_ID' => $taskSelfService->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);

        //Create a task self service value based
        $taskSelfServiceByVariable = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $taskSelfServiceByVariable->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appAssignSelfService = AppAssignSelfServiceValue::factory()->create([
            'TAS_ID' => $taskSelfServiceByVariable->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appAssignSelfService->ID,
            'GRP_UID' => $group->GRP_UID,
            'ASSIGNEE_ID' => $group->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self service value based
        Delegation::factory()->create([
            'APP_NUMBER' => $appAssignSelfService->APP_NUMBER,
            'DEL_INDEX' => $appAssignSelfService->DEL_INDEX,
            'TAS_ID' => $taskSelfServiceByVariable->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);

        //Review the self-service query
        $result = Delegation::getSelfServiceQuery($user->USR_UID);
        $this->assertTrue(is_string($result));
    }

    /**
     * This checks if get the query is working properly in self-service user and group assigned in parallel task
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfServiceQuery()
     * @test
     */
    public function it_should_get_query_cases_by_user_with_self_service_user_and_group_assigned_parallel_task()
    {
        //Create process
        $process = Process::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        //Create a task self service
        $task1 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task1
        TaskUser::factory()->create([
            'TAS_UID' => $task1->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task2 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task2
        TaskUser::factory()->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task3 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task3->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task4 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task4->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the register in self-service related to the task1
        Delegation::factory(10)->create([
            'TAS_ID' => $task1->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task2
        Delegation::factory(10)->create([
            'TAS_ID' => $task2->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task3
        Delegation::factory(10)->create([
            'TAS_ID' => $task3->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task4
        Delegation::factory(10)->create([
            'TAS_ID' => $task4->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the self-service query
        $result = Delegation::getSelfServiceQuery($user->USR_UID);
        $this->assertTrue(is_string($result));
    }

    /**
     * This checks if get the query is working properly in self-service-value-based with GRP_UID and USR_UID in parallel
     * task When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID, USR_UID]
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfServiceQuery()
     * @test
     */
    public function it_should_get_query_cases_by_user_with_self_service_value_based_usr_uid_and_grp_uid()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a task1 self service value based
        $task1 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task1->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory(10)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task1->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create a task2 self service value based
        $task2 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task2->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory(15)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task2->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the self-service query
        $result = Delegation::getSelfServiceQuery($user->USR_UID);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $result);
    }

    /**
     * This checks if get the records is working properly in self-service user assigned
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfService()
     * @test
     */
    public function it_should_get_cases_by_user_with_self_service_user_assigned()
    {
        //Create process
        $process = Process::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a task self service
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in delegation relate to self-service
        Delegation::factory(25)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the self-service records
        $result = Delegation::getSelfService($user->USR_UID);
        $this->assertEquals(25, count($result));
        $result = Delegation::getSelfService($user->USR_UID, ['APP_DELEGATION.APP_NUMBER', 'APP_DELEGATION.DEL_INDEX'], null,  null, null, null, null, 0, 15);
        $this->assertEquals(15, count($result));
    }

    /**
     * This checks if get the records is working properly in self-service-value-based when the variable has a value related
     * with the USR_UID When the value assigned in the variable @@ARRAY_OF_USERS = [USR_UID]
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfService()
     * @test
     */
    public function it_should_get_cases_by_user_with_self_service_value_based_usr_uid()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a task self service value based
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory(25)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the self-service records
        $result = Delegation::getSelfService($user->USR_UID);
        $this->assertEquals(25, count($result));
    }

    /**
     * This checks if get the records is working properly in self-service group assigned
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfService()
     * @test
     */
    public function it_should_get_cases_by_user_with_self_service_group_assigned()
    {
        //Create process
        $process = Process::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        //Create a task self service
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the register in self-service
        Delegation::factory(25)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the self-service records
        $result = Delegation::getSelfService($user->USR_UID);
        $this->assertEquals(25, count($result));
    }

    /**
     * This checks if get the records is working properly in self-service-value-based when the variable has a value related
     * with the GRP_UID When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID]
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfService()
     * @test
     */
    public function it_should_get_cases_by_user_with_self_service_value_based_grp_uid()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a task self service value based
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create a case
        $application = Application::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create([
            'USR_USERNAME' => 'gary',
            'USR_LASTNAME' => 'Gary',
            'USR_FIRSTNAME' => 'Bailey',
        ]);
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $group->GRP_UID,
            'ASSIGNEE_ID' => $group->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory(25)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the self-service records
        $result = Delegation::getSelfService($user->USR_UID);
        $this->assertEquals(25, count($result));
    }

    /**
     * This checks if get the records is working properly with self-service and self-service-value-based
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfService()
     * @test
     */
    public function it_should_get_cases_by_user_with_self_service_and_self_service_value_based()
    {
        //Create process
        $process = Process::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        //Create a task self service
        $taskSelfService = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $taskSelfService->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in self service
        Delegation::factory()->create([
            'TAS_ID' => $taskSelfService->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);

        //Create a task self service value based
        $taskSelfServiceByVariable = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $taskSelfServiceByVariable->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appAssignSelfService = AppAssignSelfServiceValue::factory()->create([
            'TAS_ID' => $taskSelfServiceByVariable->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appAssignSelfService->ID,
            'GRP_UID' => $group->GRP_UID,
            'ASSIGNEE_ID' => $group->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self service value based
        Delegation::factory()->create([
            'APP_NUMBER' => $appAssignSelfService->APP_NUMBER,
            'DEL_INDEX' => $appAssignSelfService->DEL_INDEX,
            'TAS_ID' => $taskSelfServiceByVariable->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);

        //Review the self-service records
        $result = Delegation::getSelfService($user->USR_UID);
        $this->assertEquals(2, count($result));
    }

    /**
     * This checks if get the records is working properly in self-service user and group assigned in parallel task
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfService()
     * @test
     */
    public function it_should_get_cases_by_user_with_self_service_user_and_group_assigned_parallel_task()
    {
        //Create process
        $process = Process::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        //Create a task self service
        $task1 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task1
        TaskUser::factory()->create([
            'TAS_UID' => $task1->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task2 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task2
        TaskUser::factory()->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task3 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task3->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task4 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task4->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the register in self-service related to the task1
        Delegation::factory(10)->create([
            'TAS_ID' => $task1->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task2
        Delegation::factory(10)->create([
            'TAS_ID' => $task2->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task3
        Delegation::factory(10)->create([
            'TAS_ID' => $task3->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task4
        Delegation::factory(10)->create([
            'TAS_ID' => $task4->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the self-service records
        $result = Delegation::getSelfService($user->USR_UID);
        $this->assertEquals(40, count($result));
    }

    /**
     * This checks if get the records is working properly in self-service-value-based with GRP_UID and USR_UID in parallel
     * task When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID, USR_UID]
     *
     * @covers \ProcessMaker\Model\Delegation::getSelfService()
     * @test
     */
    public function it_should_get_cases_by_user_with_self_service_value_based_usr_uid_and_grp_uid()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a task1 self service value based
        $task1 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task1->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory(10)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task1->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create a task2 self service value based
        $task2 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task2->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory(15)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task2->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the self-service records
        $result = Delegation::getSelfService($user->USR_UID);
        $this->assertEquals(25, count($result));
    }

    /**
     * This checks the counters is working properly in self-service user assigned
     *
     * @covers \ProcessMaker\Model\Delegation::countSelfService()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_user_assigned()
    {
        //Create process
        $process = Process::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a task self service
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in delegation relate to self-service
        Delegation::factory(25)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $result = Delegation::countSelfService($user->USR_UID);
        $this->assertEquals(25, $result);
    }

    /**
     * This checks the counters is working properly in self-service-value-based when the variable has a value related
     * with the USR_UID When the value assigned in the variable @@ARRAY_OF_USERS = [USR_UID]
     *
     * @covers \ProcessMaker\Model\Delegation::countSelfService()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_value_based_usr_uid()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a task self service value based
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory(25)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $result = Delegation::countSelfService($user->USR_UID);
        $this->assertEquals(25, $result);
    }

    /**
     * This checks the counters is working properly in self-service group assigned
     *
     * @covers \ProcessMaker\Model\Delegation::countSelfService()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_group_assigned()
    {
        //Create process
        $process = Process::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        //Create a task self service
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the register in self-service
        Delegation::factory(25)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $result = Delegation::countSelfService($user->USR_UID);
        $this->assertEquals(25, $result);
    }

    /**
     * This checks the counters is working properly in self-service-value-based when the variable has a value related
     * with the GRP_UID When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID]
     *
     * @covers \ProcessMaker\Model\Delegation::countSelfService()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_value_based_grp_uid()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a task self service value based
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create a case
        $application = Application::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create([
            'USR_USERNAME' => 'gary',
            'USR_LASTNAME' => 'Gary',
            'USR_FIRSTNAME' => 'Bailey',
        ]);
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $group->GRP_UID,
            'ASSIGNEE_ID' => $group->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory(25)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $result = Delegation::countSelfService($user->USR_UID);
        $this->assertEquals(25, $result);
    }

    /**
     * This checks the counters is working properly with self-service and self-service-value-based
     *
     * @covers \ProcessMaker\Model\Delegation::countSelfService()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_and_self_service_value_based()
    {
        //Create process
        $process = Process::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        //Create a task self service
        $taskSelfService = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $taskSelfService->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in self service
        Delegation::factory()->create([
            'TAS_ID' => $taskSelfService->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);

        //Create a task self service value based
        $taskSelfServiceByVariable = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $taskSelfServiceByVariable->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appAssignSelfService = AppAssignSelfServiceValue::factory()->create([
            'TAS_ID' => $taskSelfServiceByVariable->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appAssignSelfService->ID,
            'GRP_UID' => $group->GRP_UID,
            'ASSIGNEE_ID' => $group->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self service value based
        Delegation::factory()->create([
            'APP_NUMBER' => $appAssignSelfService->APP_NUMBER,
            'DEL_INDEX' => $appAssignSelfService->DEL_INDEX,
            'TAS_ID' => $taskSelfServiceByVariable->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);

        //Review the count self-service
        $result = Delegation::countSelfService($user->USR_UID);
        $this->assertEquals(2, $result);
    }

    /**
     * This checks the counters is working properly in self-service user and group assigned in parallel task
     *
     * @covers \ProcessMaker\Model\Delegation::countSelfService()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_user_and_group_assigned_parallel_task()
    {
        //Create process
        $process = Process::factory()->create();
        //Create group
        $group = Groupwf::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Assign a user in the group
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID
        ]);
        //Create a task self service
        $task1 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task1
        TaskUser::factory()->create([
            'TAS_UID' => $task1->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task2 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task2
        TaskUser::factory()->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task3 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task3->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create a task self service
        $task4 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task4->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the register in self-service related to the task1
        Delegation::factory(10)->create([
            'TAS_ID' => $task1->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task2
        Delegation::factory(10)->create([
            'TAS_ID' => $task2->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task3
        Delegation::factory(10)->create([
            'TAS_ID' => $task3->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create the register in self-service related to the task4
        Delegation::factory(10)->create([
            'TAS_ID' => $task4->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $result = Delegation::countSelfService($user->USR_UID);
        $this->assertEquals(40, $result);
    }

    /**
     * This checks the counters is working properly in self-service-value-based with GRP_UID and USR_UID in parallel
     * task When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID, USR_UID]
     *
     * @covers \ProcessMaker\Model\Delegation::countSelfService()
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_value_based_usr_uid_and_grp_uid()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a task1 self service value based
        $task1 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task1->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory(10)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task1->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create a task2 self service value based
        $task2 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = AppAssignSelfServiceValue::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task2->TAS_ID
        ]);
        AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self-service
        Delegation::factory(15)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task2->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $result = Delegation::countSelfService($user->USR_UID);
        $this->assertEquals(25, $result);
    }

    /**
     * This check if return the USR_UID assigned in the thread OPEN
     *
     * @covers \ProcessMaker\Model\Delegation::getCurrentUser()
     * @test
     */
    public function it_should_return_current_user_for_thread_open()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a delegation
        Delegation::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
        ]);
        Delegation::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'DEL_INDEX' => 1,
            'USR_UID' => $user->USR_UID,
        ]);

        //Get the current user assigned in the open thread
        $result = Delegation::getCurrentUser($application->APP_NUMBER, 2, 'OPEN');
        $this->assertEquals($user->USR_UID, $result);
    }

    /**
     * This check if return the USR_UID assigned in the thread CLOSED
     *
     * @covers \ProcessMaker\Model\Delegation::getCurrentUser()
     * @test
     */
    public function it_should_return_current_user_for_thread_closed()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a delegation
        Delegation::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'DEL_INDEX' => 1,
            'USR_UID' => $user->USR_UID,
        ]);
        Delegation::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
        ]);

        //Get the current user assigned in the open thread
        $result = Delegation::getCurrentUser($application->APP_NUMBER, 1, 'CLOSED');
        $this->assertEquals($user->USR_UID, $result);
    }

    /**
     * This check if return empty when the data does not exits
     *
     * @covers \ProcessMaker\Model\Delegation::getCurrentUser()
     * @test
     */
    public function it_should_return_empty_when_row_does_not_exist()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create a delegation
        Delegation::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'DEL_INDEX' => 1,
            'USR_UID' => $user->USR_UID,
        ]);
        Delegation::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
        ]);

        //Get the current user assigned in the open thread
        $result = Delegation::getCurrentUser($application->APP_NUMBER, 3, 'CLOSED');
        $this->assertEmpty($result);

        $result = Delegation::getCurrentUser($application->APP_NUMBER, 3, 'OPEN');
        $this->assertEmpty($result);
    }

    /**
     * This checks if return the open thread
     *
     * @covers \ProcessMaker\Model\Delegation::getOpenThread()
     * @test
     */
    public function it_should_return_thread_open()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create task
        $task = Task::factory()->create();
        //Create a delegation
        $delegation = Delegation::factory()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_FINISH_DATE' => null,
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_UID' => $task->TAS_UID,
        ]);
        $result = Delegation::getOpenThread($application->APP_NUMBER, $delegation->DEL_INDEX);
        $this->assertEquals($application->APP_NUMBER, $result['APP_NUMBER']);
    }

    /**
     * This checks if return empty when the thread is CLOSED
     *
     * @covers \ProcessMaker\Model\Delegation::getOpenThread()
     * @test
     */
    public function it_should_return_empty_when_thread_is_closed()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create task
        $task = Task::factory()->create();
        //Create a delegation
        $delegation = Delegation::factory()->create([
            'DEL_THREAD_STATUS' => 'CLOSED',
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_UID' => $task->TAS_UID,
        ]);
        $result = Delegation::getOpenThread($application->APP_NUMBER, $delegation->DEL_INDEX);
        $this->assertEmpty($result);
    }

    /**
     * This checks if return empty when the data is not null
     *
     * @covers \ProcessMaker\Model\Delegation::getOpenThread()
     * @test
     */
    public function it_should_return_empty_when_thread_finish_date_is_not_null()
    {
        //Create process
        $process = Process::factory()->create();
        //Create a case
        $application = Application::factory()->create();
        //Create user
        $user = User::factory()->create();
        //Create task
        $task = Task::factory()->create();
        //Create a delegation
        $delegation = Delegation::factory()->create([
            'DEL_THREAD_STATUS' => 'CLOSED',
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_UID' => $task->TAS_UID,
        ]);
        $result = Delegation::getOpenThread($application->APP_NUMBER, $delegation->DEL_INDEX);
        $this->assertEmpty($result);
    }

    /**
     * This checks if return the participation when the user does have participation
     *
     * @covers \ProcessMaker\Model\Delegation::participation()
     * @test
     */
    public function it_when_the_user_does_have_participation()
    {
        Process::factory()->create();
        //Create user
        $user = User::factory()->create();
        $application = Application::factory()->create();
        //Create a delegation
        Delegation::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'USR_ID' => $user->USR_ID,
            'USR_UID' => $user->USR_UID
        ]);
        $result = Delegation::participation($application->APP_UID, $user->USR_UID);
        $this->assertTrue($result);
    }

    /**
     * This checks if return the participation of the user when the user does not have participation
     *
     * @covers \ProcessMaker\Model\Delegation::participation()
     * @test
     */
    public function it_when_the_user_does_not_have_participation()
    {
        Process::factory()->create();
        //Create user
        $user = User::factory()->create();
        $application = Application::factory()->create();
        //Create a delegation
        Delegation::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID
        ]);
        $result = Delegation::participation($application->APP_UID, $user->USR_UID);
        $this->assertFalse($result);
    }

    /**
     * This check the return of thread title
     *
     * @covers \ProcessMaker\Model\Delegation::getThreadTitle()
     * @test
     */
    public function it_get_thread_title()
    {
        $delegation = Delegation::factory()->foreign_keys()->create();
        $result = Delegation::getThreadTitle($delegation->TAS_UID, $delegation->APP_NUMBER, $delegation->DEL_PREVIOUS, []);
        $this->assertNotEmpty($result);
    }

    /**
     * This check the return of thread info
     *
     * @covers \ProcessMaker\Model\Delegation::getThreadInfo()
     * @test
     */
    public function it_get_thread_info()
    {
        $delegation = Delegation::factory()->foreign_keys()->create();
        $result = Delegation::getThreadInfo($delegation->APP_NUMBER, $delegation->DEL_INDEX);
        $this->assertNotEmpty($result);
    }

    /**
     * This check the return of thread info
     *
     * @covers \ProcessMaker\Model\Delegation::getDatesFromThread()
     * @test
     */
    public function it_get_thread_dates()
    {
        $delegation = Delegation::factory()->foreign_keys()->create();
        $task = new Task();
        $taskInfo = $task->load($delegation->TAS_UID);
        $taskInfo = head($taskInfo);
        $taskType = $taskInfo['TAS_TYPE'];
        $result = Delegation::getDatesFromThread(
            $delegation->APP_UID,
            $delegation->DEL_INDEX,
            $delegation->TAS_UID,
            $taskType
        );
        $this->assertNotEmpty($result);
    }

    /**
     * This check the return of pending threads
     *
     * @covers \ProcessMaker\Model\Delegation::getPendingThreads()
     * @test
     */
    public function it_get_threads_pending()
    {
        $delegation = Delegation::factory()->foreign_keys()->create();
        $result = Delegation::getPendingThreads($delegation->APP_NUMBER);
        $this->assertNotEmpty($result);
        $result = Delegation::getPendingThreads($delegation->APP_NUMBER, false);
        $this->assertNotEmpty($result);
    }

    /**
     * This check the return of pending task
     *
     * @covers \ProcessMaker\Model\Delegation::getPendingTask()
     * @test
     */
    public function it_get_task_pending()
    {
        $delegation = Delegation::factory()->foreign_keys()->create();
        $result = Delegation::getPendingTask($delegation->APP_NUMBER);
        $this->assertNotEmpty($result);
    }

    /**
     * This check the return of last thread
     *
     * @covers \ProcessMaker\Model\Delegation::getLastThread()
     * @test
     */
    public function it_get_last_thread()
    {
        $delegation = Delegation::factory()->foreign_keys()->create();
        $result = Delegation::getLastThread($delegation->APP_NUMBER);
        $this->assertNotEmpty($result);
    }

    /**
     * This tests the getDeltitle() method
     *
     * @covers \ProcessMaker\Model\Delegation::getDeltitle()
     * @test
     */
    public function it_should_test_the_get_del_title_method()
    {
        $delegation = Delegation::factory()->create([
            'DEL_TITLE' => "test"
        ]);
        $result = Delegation::getDeltitle($delegation->APP_NUMBER, $delegation->DEL_INDEX);
        $this->assertNotEmpty($result);
        $this->assertEquals($result, $delegation->DEL_TITLE);
    }

    /**
     * It should test the hasActiveParentsCases() method
     * 
     * @covers \ProcessMaker\Model\Delegation::hasActiveParentsCases()
     * @test
     */
    public function it_should_test_the_has_active_parents_cases_method()
    {
        $process = Process::factory()->create();
        $processParent = Process::factory(3)->create();
        SubProcess::factory()->create([
            'PRO_UID' => $process['PRO_UID'],
            'PRO_PARENT' => $processParent[0]['PRO_UID']
        ]);
        SubProcess::factory()->create([
            'PRO_UID' => $process['PRO_UID'],
            'PRO_PARENT' => $processParent[1]['PRO_UID']
        ]);
        SubProcess::factory()->create([
            'PRO_UID' => $process['PRO_UID'],
            'PRO_PARENT' => $processParent[2]['PRO_UID']
        ]);

        $parents = SubProcess::getProParents($process['PRO_UID']);

        Delegation::factory()->create([
            'PRO_UID' => $parents[0]['PRO_PARENT'],
            'TAS_UID' => $parents[0]['TAS_PARENT'],
            'DEL_THREAD_STATUS' => 'OPEN'
        ]);

        $res = Delegation::hasActiveParentsCases($parents);
        $this->assertTrue($res);
        $res = Delegation::hasActiveParentsCases([]);
        $this->assertFalse($res);
    }

    /**
     * This check the return cases completed by specific user
     *
     * @covers \ProcessMaker\Model\Delegation::casesCompletedBy()
     * @test
     */
    public function it_get_cases_completed_by_specific_user()
    {
        $delegation = Delegation::factory()->last_thread()->create();
        $result = Delegation::casesCompletedBy($delegation->USR_ID);
        $this->assertNotEmpty($result);
    }

    /**
     * This check the return cases completed by specific user
     *
     * @covers \ProcessMaker\Model\Delegation::casesStartedBy()
     * @test
     */
    public function it_get_cases_started_by_specific_user()
    {
        $delegation = Delegation::factory()->first_thread()->create();
        $result = Delegation::casesStartedBy($delegation->USR_ID);
        $this->assertNotEmpty($result);
    }

    /**
     * This check the return cases thread title
     *
     * @covers \ProcessMaker\Model\Delegation::casesThreadTitle()
     * @test
     */
    public function it_get_cases_thread_title()
    {
        $delegation = Delegation::factory()->foreign_keys()->create([
            'DEL_INDEX' => 1,
            'DEL_PREVIOUS' => 0
        ]);
        $result = Delegation::casesThreadTitle($delegation->DEL_TITLE);
        $this->assertTrue(isset($result[0]));
    }

    /**
     * Test the scopeParticipatedUser
     *
     * @covers \ProcessMaker\Model\Delegation::scopeParticipatedUser()
     * @test
     */
    public function it_return_scope_participated_user()
    {
        $application = Application::factory()->completed()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $res = $table->joinApplication()->participatedUser($table->USR_ID)->get();
        $this->assertCount(1, $res);
    }

    /**
     * Test the scopeInboxMetrics
     *
     * @covers \ProcessMaker\Model\Delegation::scopeInboxMetrics()
     * @test
     */
    public function it_tests_scope_inbox_metrics()
    {
        $application = Application::factory()->todo()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $res = $table->inboxMetrics()->get();
        $this->assertCount(1, $res);
    }

    /**
     * Test the scopeDraftMetrics
     *
     * @covers \ProcessMaker\Model\Delegation::scopeDraftMetrics()
     * @test
     */
    public function it_tests_scope_draft_metrics()
    {
        $application = Application::factory()->draft()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $res = $table->draftMetrics()->get();
        $this->assertCount(1, $res);
    }

    /**
     * Test the scopePausedMetrics
     *
     * @covers \ProcessMaker\Model\Delegation::scopePausedMetrics()
     * @test
     */
    public function it_tests_scope_paused_metrics()
    {
        $application = Application::factory()->paused()->create();
        $appDelay = AppDelay::factory()->paused_foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $appDelay->APP_DEL_INDEX,
        ]);
        $res = $table->pausedMetrics()->get();
        $this->assertCount(1, $res);
    }

    /**
     * Test the scopeSelfServiceMetrics
     *
     * @covers \ProcessMaker\Model\Delegation::scopeSelfServiceMetrics()
     * @test
     */
    public function it_tests_scope_self_service_metrics()
    {
        $application = Application::factory()->paused()->create();
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
        ]);
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        $res = $delegation->selfServiceMetrics()->get();
        $this->assertCount(1, $res);
    }
}