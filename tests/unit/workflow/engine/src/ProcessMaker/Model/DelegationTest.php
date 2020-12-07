<?php

namespace Tests\unit\workflow\src\ProcessMaker\Model;

use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\AppAssignSelfServiceValue;
use ProcessMaker\Model\AppAssignSelfServiceValueGroup;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\GroupUser;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessCategory;
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
    use DatabaseTransactions;

    /**
     * Set up function.
     */
    public function setUp()
    {
        parent::setUp();
        Delegation::truncate();
    }

    /**
     * This test scopePriority
     *
     * @covers \ProcessMaker\Model\Delegation::scopePriority()
     * @test
     */
    public function it_return_scope_priority()
    {
        $table = factory(Delegation::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->priority($table->DEL_PRIORITY)->get());
    }

    /**
     * This test scopeIndex
     *
     * @covers \ProcessMaker\Model\Delegation::scopeIndex()
     * @test
     */
    public function it_return_scope_index()
    {
        $table = factory(Delegation::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->index($table->DEL_INDEX)->get());
    }

    /**
     * This test scopeCaseStarted
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCaseStarted()
     * @test
     */
    public function it_return_scope_case_started()
    {
        $table = factory(Delegation::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->caseStarted($table->DEL_INDEX)->get());
    }

    /**
     * This test scopeCaseInProgress
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCaseInProgress()
     * @test
     */
    public function it_return_scope_case_in_progress()
    {
        $table = factory(Delegation::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->joinApplication()->caseInProgress()->get());
    }

    /**
     * This test scopeCaseCompleted
     *
     * @covers \ProcessMaker\Model\Delegation::scopeCaseCompleted()
     * @test
     */
    public function it_return_scope_case_in_completed()
    {
        $application = factory(Application::class)->states('completed')->create();
        $table = factory(Delegation::class)->states('foreign_keys')->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $this->assertCount(1, $table->joinApplication()->caseCompleted()->get());
    }

    /**
     * This test scopeDelegateDateFrom
     *
     * @covers \ProcessMaker\Model\Delegation::scopeDelegateDateFrom()
     * @test
     */
    public function it_return_scope_delegate_date_from()
    {
        $table = factory(Delegation::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->delegateDateFrom($table->DEL_DELEGATE_DATE)->get());
    }

    /**
     * This test scopeDelegateDateTo
     *
     * @covers \ProcessMaker\Model\Delegation::scopeDelegateDateTo()
     * @test
     */
    public function it_return_scope_delegate_date_to()
    {
        $table = factory(Delegation::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->delegateDateTo($table->DEL_DELEGATE_DATE)->get());
    }

    /**
     * This test scopeSpecificCases
     *
     * @covers \ProcessMaker\Model\Delegation::scopeSpecificCases()
     * @test
     */
    public function it_return_scope_specific_cases()
    {
        $table = factory(Delegation::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->specificCases([$table->APP_NUMBER])->get());
    }

    /**
     * This test scopeWithoutUserId
     *
     * @covers \ProcessMaker\Model\Delegation::scopeWithoutUserId()
     * @test
     */
    public function it_return_scope_without_user_id()
    {
        $table = factory(Delegation::class)->states('foreign_keys')->create([
            'USR_ID' => 0
        ]);
        $this->assertCount(1, $table->withoutUserId($table->TAS_ID)->get());
    }

    /**
     * This test scopeTask
     *
     * @covers \ProcessMaker\Model\Delegation::scopeTask()
     * @test
     */
    public function it_return_scope_task()
    {
        $table = factory(Delegation::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->task()->get());
    }

    /**
     * This test scopeSpecificTasks
     *
     * @covers \ProcessMaker\Model\Delegation::scopeSpecificTasks()
     * @test
     */
    public function it_return_scope_specific_tasks()
    {
        $table = factory(Delegation::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->specificTasks([$table->TAS_ID])->get());
    }

    /**
     * This checks to make sure pagination is working properly
     *
     * @covers \ProcessMaker\Model\Delegation::search()
     * @test
     */
    public function it_should_return_pages_of_data()
    {
        factory(Delegation::class, 51)->states('foreign_keys')->create();
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
        factory(Delegation::class, 50)->states('foreign_keys')->create();
        factory(Delegation::class, 1)->states('foreign_keys')->create([
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

        $process = factory(Process::class)->create();
        factory(Delegation::class, 51)->states('foreign_keys')->create([
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
        $application = factory(Application::class)->create([
            'APP_STATUS_ID' => 1,
            'APP_STATUS' => 'DRAFT'
        ]);
        factory(Delegation::class, 51)->states('foreign_keys')->create([
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
        $application = factory(Application::class)->create([
            'APP_STATUS_ID' => 2,
            'APP_STATUS' => 'TO_DO'
        ]);
        factory(Delegation::class, 51)->states('foreign_keys')->create([
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
        $application = factory(Application::class)->create([
            'APP_STATUS_ID' => 3,
            'APP_STATUS' => 'COMPLETED',
        ]);
        factory(Delegation::class, 51)->states('foreign_keys')->create([
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
        $application = factory(Application::class)->create([
            'APP_STATUS_ID' => 4,
            'APP_STATUS' => 'CANCELLED'
        ]);
        factory(Delegation::class, 51)->states('foreign_keys')->create([
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
        $user = factory(User::class)->create();
        // Create a new delegation, but for this specific user
        factory(Delegation::class)->states('foreign_keys')->create([
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
            $application = factory(Application::class)->create();
            factory(Delegation::class)->states('foreign_keys')->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'DEL_THREAD_STATUS' => 'OPEN'
            ]);
        }
        // Searching by a existent case number, result ordered by APP_NUMBER, filter by APP_NUMBER in DESC mode
        $results = Delegation::search(null, 0, 25, $application->APP_NUMBER, null, null, 'DESC',
            'APP_NUMBER', null, null, null, 'APP_NUMBER');
        $this->assertCount(1, $results['data']);
        $this->assertEquals($application->APP_NUMBER, $results['data'][0]['APP_NUMBER']);
        // Searching by a existent case number, result ordered by APP_NUMBER, filter by APP_NUMBER in ASC mode
        $results = Delegation::search(null, 0, 25, $application->APP_NUMBER, null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_NUMBER');
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
        $delegations = factory(Delegation::class, 1)
                ->states('foreign_keys')
                ->create();
        $title = $delegations->last()
                ->application
                ->APP_TITLE;
        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();

        // Searching by a existent case title, result ordered by APP_NUMBER, filter by APP_NUMBER in DESC mode
        $results = Delegation::search(null, 0, 10, $title, null, null, 'DESC',
                        'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(1, $results['data']);
        $this->assertEquals($title, $results['data'][0]['APP_TITLE']);
        // Searching by a existent case title, result ordered by APP_NUMBER, filter by APP_NUMBER in ASC mode
        $results = Delegation::search(null, 0, 10, $title, null, null, 'ASC',
                        'APP_NUMBER', null, null, null, 'APP_TITLE');
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
        factory(Delegation::class, 5)->states('foreign_keys')->create([
            'TAS_ID' => function () {
                return factory(Task::class)->create()->TAS_ID;
            }
        ]);
        $task = factory(Task::class)->create();
        factory(Delegation::class)->states('foreign_keys')->create([
            'TAS_ID' => $task->TAS_ID
        ]);
        // Get the order taskTitle in ASC mode
        $results = Delegation::search(null, 0, 10, $task->TAS_TITLE, null, null, 'ASC',
            'TAS_TITLE', null, null, null, 'TAS_TITLE');
        $this->assertCount(1, $results['data']);
        $this->assertEquals($task->TAS_TITLE, $results['data'][0]['APP_TAS_TITLE']);
        $results = Delegation::search(null, 0, 10, null, null, null, 'ASC',
            'TAS_TITLE', null, null, null, 'TAS_TITLE');
        $this->assertGreaterThan($results['data'][0]['APP_TAS_TITLE'], $results['data'][1]['APP_TAS_TITLE']);

        // Get the order taskTitle in DESC mode
        $results = Delegation::search(null, 0, 10, $task->TAS_TITLE, null, null, 'DESC',
            'TAS_TITLE', null, null, null, 'TAS_TITLE');
        $this->assertCount(1, $results['data']);
        $this->assertEquals($task->TAS_TITLE, $results['data'][0]['APP_TAS_TITLE']);
        $results = Delegation::search(null, 0, 10, null, null, null, 'DESC',
            'TAS_TITLE', null, null, null, 'TAS_TITLE');
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
        factory(Delegation::class, 2)->states('foreign_keys')->create();
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
        factory(Delegation::class, 2)->states('foreign_keys')->create();
        // Get first page, the minor case title
        $results = Delegation::search(null, 0, 2, null, null, null, 'ASC', 'APP_TITLE');
        $this->assertCount(2, $results['data']);
        $this->assertGreaterThan($results['data'][0]['APP_TITLE'], $results['data'][1]['APP_TITLE']);
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
        factory(Delegation::class, 3)->states('foreign_keys')->create([
            'PRO_ID' => function () {
                return factory(Process::class)->create()->PRO_ID;
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
        factory(Delegation::class, 2)->states('foreign_keys')->create([
            'TAS_ID' => function () {
                return factory(Task::class)->create()->TAS_ID;
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
        factory(Delegation::class, 2)->states('foreign_keys')->create([
            'USR_ID' => function () {
                return factory(User::class)->create()->USR_ID;
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
        factory(Delegation::class, 2)->states('foreign_keys')->create([
            'APP_NUMBER' => function () {
                return factory(Application::class)->create()->APP_NUMBER;
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
        factory(Delegation::class, 10)->states('foreign_keys')->create();
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
        factory(Delegation::class)->states('foreign_keys')->create([
            'APP_NUMBER' => function () {
                return factory(Application::class)->create(['APP_STATUS' => 'DRAFT', 'APP_STATUS_ID' => 1])->APP_NUMBER;
            }
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'APP_NUMBER' => function () {
                return factory(Application::class)->create(['APP_STATUS' => 'TO_DO', 'APP_STATUS_ID' => 2])->APP_NUMBER;
            }
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'APP_NUMBER' => function () {
                return factory(Application::class)->create([
                    'APP_STATUS' => 'COMPLETED',
                    'APP_STATUS_ID' => 3
                ])->APP_NUMBER;
            }
        ]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'APP_NUMBER' => function () {
                return factory(Application::class)->create([
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
        factory(ProcessCategory::class, 4)->create();
        factory(Process::class, 4)->create([
            'PRO_CATEGORY' => ProcessCategory::all()->random()->CATEGORY_UID
        ]);
        // Dummy Delegations
        factory(Delegation::class, 100)->create([
            'PRO_ID' => Process::all()->random()->PRO_ID
        ]);
        // Process with the category to search
        $category = factory(ProcessCategory::class)->create();
        $processSearch = factory(Process::class)->create([
            'PRO_CATEGORY' => $category->CATEGORY_UID
        ]);
        // Delegations to found
        factory(Delegation::class, 51)->states('foreign_keys')->create([
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
        factory(Delegation::class, 10)->states('foreign_keys')->create([
            'DEL_DELEGATE_DATE' => '2019-01-02 00:00:00'
        ]);
        factory(Delegation::class, 10)->states('foreign_keys')->create([
            'DEL_DELEGATE_DATE' => '2019-01-03 00:00:00'
        ]);
        factory(Delegation::class, 10)->states('foreign_keys')->create([
            'DEL_DELEGATE_DATE' => '2019-01-04 00:00:00'
        ]);
        factory(Delegation::class, 10)->states('foreign_keys')->create([
            'DEL_DELEGATE_DATE' => '2019-01-05 00:00:00'
        ]);
        $results = Delegation::search(null, 0, 25, null, null, null, null, null, null, '2019-01-02 00:00:00',
            '2019-01-03 00:00:00');
        $this->assertCount(20, $results['data']);
        foreach ($results['data'] as $value) {
            $this->assertGreaterThanOrEqual('2019-01-02 00:00:00', $value['DEL_DELEGATE_DATE']);
            $this->assertLessThanOrEqual('2019-01-03 00:00:00', $value['DEL_DELEGATE_DATE']);
            $this->assertRegExp('(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) ', $value['DEL_DELEGATE_DATE']);
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
        factory(Delegation::class, 10)->states('foreign_keys')->create([
            'DEL_DELEGATE_DATE' => '2019-01-02 00:00:00'
        ]);
        factory(Delegation::class, 10)->states('foreign_keys')->create([
            'DEL_DELEGATE_DATE' => '2019-01-03 00:00:00'
        ]);
        factory(Delegation::class, 10)->states('foreign_keys')->create([
            'DEL_DELEGATE_DATE' => '2019-01-04 00:00:00'
        ]);
        factory(Delegation::class, 10)->states('foreign_keys')->create([
            'DEL_DELEGATE_DATE' => '2019-01-05 00:00:00'
        ]);
        // Search setting only from
        $results = Delegation::search(
            null, 0, 40, null, null, null, null, null, null, '2019-01-02 00:00:00'
        );
        $this->assertCount(40, $results['data']);
        foreach ($results['data'] as $value) {
            $this->assertGreaterThanOrEqual('2019-01-02 00:00:00', $value['DEL_DELEGATE_DATE']);
            $this->assertRegExp('(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) ', $value['DEL_DELEGATE_DATE']);
        }
        // Search setting only to
        $results = Delegation::search(
            null, 0, 40, null, null, null, null, null, null, null, '2019-01-04 00:00:00'
        );
        foreach ($results['data'] as $value) {
            $this->assertLessThanOrEqual('2019-01-04 00:00:00', $value['DEL_DELEGATE_DATE']);
            $this->assertRegExp('(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) ', $value['DEL_DELEGATE_DATE']);
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
        $process = factory(Process::class)->create();
        // Create a task
        $parallelTask = factory(Task::class)->create();
        // Create a case
        $application = factory(Application::class)->create();
        // Create the threads for a parallel process
        factory(Delegation::class, 5)->states('foreign_keys')->create([
            'PRO_ID' => $process->PRO_ID,
            'TAS_ID' => $parallelTask->TAS_ID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'CLOSED'
        ]);
        // Get first page, searching for threads are closed
        $results = Delegation::search(null, 0, 5, $application->APP_NUMBER, null, null, null,
            'TAS_TITLE', null, null, null, 'TAS_TITLE');
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
        $process = factory(Process::class)->create();
        // Create a task
        $parallelTask = factory(Task::class)->create();
        // Create a case
        $application = factory(Application::class)->create();
        // Create the threads for a parallel process
        factory(Delegation::class, 5)->states('foreign_keys')->create([
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
        $process = factory(Process::class)->create();
        // Create a task
        $parallelTask = factory(Task::class)->create();
        // Create a case
        $application = factory(Application::class)->create(['APP_STATUS_ID' => 2]);
        // Create the threads for a parallel process closed
        factory(Delegation::class)->states('closed')->create([
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_ID' => $parallelTask->TAS_ID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 1
        ]);
        // Create the threads for a parallel process closed
        factory(Delegation::class)->states('open')->create([
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_ID' => $parallelTask->TAS_ID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2
        ]);
        // Get first page, searching for threads are open
        $results = Delegation::search(null, 0, 5, $application->APP_NUMBER, null, null, null, null, null, null, null,
            'APP_NUMBER');

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
        factory(Delegation::class, 5)->states('foreign_keys')->create([
            'APP_NUMBER' => function () {
                return factory(Application::class)->create(['APP_STATUS' => ''])->APP_NUMBER;
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
        $category = factory(ProcessCategory::class, 2)->create();
        //Create a process with category
        $processWithCat = factory(Process::class)->create(['PRO_CATEGORY' => $category[0]->CATEGORY_UID]);
        factory(Delegation::class)->states('foreign_keys')->create([
            'PRO_ID' => $processWithCat->PRO_ID
        ]);
        // Create a process without category
        $processWithoutCat = factory(Process::class)->create(['PRO_CATEGORY' => '']);
        factory(Delegation::class, 5)->states('foreign_keys')->create([
            'PRO_ID' => $processWithoutCat->PRO_ID
        ]);
        // Search the cases when the process has related to the category and search by another category
        $results = Delegation::search(null, 0, 25, null, $processWithCat->PRO_ID, null, null, null,
            $category[1]->CATEGORY_UID);
        $this->assertCount(0, $results['data']);
        // Search the cases when the process has related to the category and search by this relation
        $results = Delegation::search(null, 0, 25, null, $processWithCat->PRO_ID, null, null, null,
            $category[0]->CATEGORY_UID);
        $this->assertCount(1, $results['data']);
        // Search the cases when the process does not have relation with category and search by a category
        $results = Delegation::search(null, 0, 25, null, $processWithoutCat->PRO_ID, null, null, null,
            $category[1]->CATEGORY_UID);
        $this->assertCount(0, $results['data']);
        // Search the cases when the process does not have relation with category empty
        $results = Delegation::search(null, 0, 25, null, $processWithoutCat->PRO_ID, null, null, null,
            '');
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
        $category = factory(ProcessCategory::class)->create();
        //Define a process related with he previous category
        $processWithCat = factory(Process::class)->create([
            'PRO_CATEGORY' => $category->CATEGORY_UID
        ]);
        //Create a delegation related to this process
        factory(Delegation::class)->create([
            'PRO_ID' => $processWithCat->PRO_ID
        ]);
        //Define a process related with he previous category
        $process = factory(Process::class)->create([
            'PRO_CATEGORY' => ''
        ]);
        //Create a delegation related to other process
        factory(Delegation::class, 5)->create([
            'PRO_ID' => $process->PRO_ID,
        ]);

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
        factory(User::class, 100)->create();
        $process = factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'APP_UID' => G::generateUniqueID()
        ]);
        factory(Delegation::class)->states('closed')->create([
            'PRO_UID' => $process->PRO_UID,
            'APP_UID' => $application->APP_UID
        ]);
        factory(Delegation::class)->states('open')->create([
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
        $taskSelfService = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $taskSelfService->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in self service
        factory(Delegation::class)->create([
            'TAS_ID' => $taskSelfService->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);

        //Create a task self service value based
        $taskSelfServiceByVariable = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $taskSelfServiceByVariable->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appAssignSelfService = factory(AppAssignSelfServiceValue::class)->create([
            'TAS_ID' => $taskSelfServiceByVariable->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class)->create([
            'ID' => $appAssignSelfService->ID,
            'GRP_UID' => $group->GRP_UID,
            'ASSIGNEE_ID' => $group->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self service value based
        factory(Delegation::class)->create([
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
        //Review the self-service records
        $result = Delegation::getSelfService($user->USR_UID);
        $this->assertEquals(25, count($result));
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
        $taskSelfService = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $taskSelfService->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in self service
        factory(Delegation::class)->create([
            'TAS_ID' => $taskSelfService->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);

        //Create a task self service value based
        $taskSelfServiceByVariable = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $taskSelfServiceByVariable->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appAssignSelfService = factory(AppAssignSelfServiceValue::class)->create([
            'TAS_ID' => $taskSelfServiceByVariable->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class)->create([
            'ID' => $appAssignSelfService->ID,
            'GRP_UID' => $group->GRP_UID,
            'ASSIGNEE_ID' => $group->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self service value based
        factory(Delegation::class)->create([
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
        $taskSelfService = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $taskSelfService->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in self service
        factory(Delegation::class)->create([
            'TAS_ID' => $taskSelfService->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);

        //Create a task self service value based
        $taskSelfServiceByVariable = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $taskSelfServiceByVariable->TAS_UID,
            'USR_UID' => $group->GRP_UID,
            'TU_RELATION' => 2, //Related to the group
            'TU_TYPE' => 1
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appAssignSelfService = factory(AppAssignSelfServiceValue::class)->create([
            'TAS_ID' => $taskSelfServiceByVariable->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class)->create([
            'ID' => $appAssignSelfService->ID,
            'GRP_UID' => $group->GRP_UID,
            'ASSIGNEE_ID' => $group->GRP_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 2 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self service value based
        factory(Delegation::class)->create([
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
        $process = factory(Process::class)->create();
        //Create a case
        $application = factory(Application::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a delegation
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'USR_UID' => $user->USR_UID,
        ]);
        factory(Delegation::class)->create([
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
        $process = factory(Process::class)->create();
        //Create a case
        $application = factory(Application::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a delegation
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'DEL_INDEX' => 1,
            'USR_UID' => $user->USR_UID,
        ]);
        factory(Delegation::class)->create([
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
        $process = factory(Process::class)->create();
        //Create a case
        $application = factory(Application::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a delegation
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'DEL_INDEX' => 1,
            'USR_UID' => $user->USR_UID,
        ]);
        factory(Delegation::class)->create([
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
     * @covers \ProcessMaker\Model\Delegation::getOpenThreads()
     * @test
     */
    public function it_should_return_thread_open()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create a case
        $application = factory(Application::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create task
        $task = factory(Task::class)->create();
        //Create a delegation
        factory(Delegation::class)->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_FINISH_DATE' => null,
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_UID' => $task->TAS_UID,
        ]);
        $result = Delegation::getOpenThreads($application->APP_NUMBER, $task->TAS_UID);
        $this->assertEquals($application->APP_NUMBER, $result['APP_NUMBER']);
    }

    /**
     * This checks if return empty when the thread is CLOSED
     *
     * @covers \ProcessMaker\Model\Delegation::getOpenThreads()
     * @test
     */
    public function it_should_return_empty_when_thread_is_closed()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create a case
        $application = factory(Application::class)->create();
        //Create task
        $task = factory(Task::class)->create();
        //Create a delegation
        factory(Delegation::class)->create([
            'DEL_THREAD_STATUS' => 'CLOSED',
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_UID' => $task->TAS_UID,
        ]);
        $result = Delegation::getOpenThreads($application->APP_NUMBER, $task->TAS_UID);
        $this->assertEmpty($result);
    }

    /**
     * This checks if return empty when the data is not null
     *
     * @covers \ProcessMaker\Model\Delegation::getOpenThreads()
     * @test
     */
    public function it_should_return_empty_when_thread_finish_date_is_not_null()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create a case
        $application = factory(Application::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create task
        $task = factory(Task::class)->create();
        //Create a delegation
        factory(Delegation::class)->create([
            'DEL_THREAD_STATUS' => 'CLOSED',
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_UID' => $task->TAS_UID,
        ]);
        $result = Delegation::getOpenThreads($application->APP_NUMBER, $task->TAS_UID);
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
        factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        $application = factory(Application::class)->create();
        //Create a delegation
        factory(Delegation::class)->create([
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
        factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        $application = factory(Application::class)->create();
        //Create a delegation
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID
        ]);
        $result = Delegation::participation($application->APP_UID, $user->USR_UID);
        $this->assertFalse($result);
    }
}