<?php

namespace Tests\unit\workflow\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\BusinessModel\Cases\Draft;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Draft
 */
class DraftTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This checks the counters is working properly in draft
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getCounter()
     * @test
     */
    public function it_should_count_cases()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a task related with the process
        $task = factory(Task::class)->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create application and app_delegation related with DRAFT status
        $casesDraft = 25;
        for ($x = 1; $x <= $casesDraft; $x++) {
            $application = factory(Application::class)->states('draft')->create();
            factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'USR_ID' => $user->USR_ID,
            ]);
        }

        //Create application and app_delegation related with OTHER status
        $casesTodo = 25;
        for ($x = 1; $x <= $casesTodo; $x++) {
            $application = factory(Application::class)->states('todo')->create();
            factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'USR_ID' => $user->USR_ID,
            ]);
        }

        //Review the count draft
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $result = $draft->getCounter();
        $this->assertEquals($casesDraft, $result);
    }

    /**
     * This checks to make sure pagination is working properly in draft
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_return_draft_paged()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a task
        $task = factory(Task::class)->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create application and app_delegation related with DRAFT status
        $casesDraft = 51;
        for ($x = 1; $x <= $casesDraft; $x++) {
            $application = factory(Application::class)->states('draft')->create();
            factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'USR_ID' => $user->USR_ID,
            ]);
        }

        // Get first page
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        $draft->setOffset(0);
        $draft->setLimit(25);
        $results = $draft->getData();
        $this->assertCount(25, $results);
        // Get second page
        $draft->setOffset(25);
        $draft->setLimit(25);
        $results = $draft->getData();
        $this->assertCount(25, $results);
        // Get third page
        $draft->setOffset(50);
        $draft->setLimit(25);
        $results = $draft->getData();
        $this->assertCount(1, $results);
    }

    /**
     * This ensures ordering ascending and descending works by case number APP_NUMBER in draft
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_return_draft_sort_by_case_number()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a task
        $task = factory(Task::class)->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create application and app_delegation related with DRAFT status with a minor case number
        $application = factory(Application::class)->states('draft_minor_case')->create();
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'USR_ID' => $user->USR_ID,
        ]);
        //Create application and app_delegation related with DRAFT status with a minor case number
        $application2 = factory(Application::class)->states('draft_major_case')->create();
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'USR_ID' => $user->USR_ID,
        ]);
        //Get the data ordered by APP_NUMBER
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        // Get first page, the minor case id
        $draft->setOrderDirection('ASC');
        $results = $draft->getData();
        $this->assertGreaterThan($results[0]['APP_NUMBER'], $results[1]['APP_NUMBER']);
        // Get first page, the major case id
        $draft->setOrderDirection('DESC');
        $results = $draft->getData();
        $this->assertLessThan($results[0]['APP_NUMBER'], $results[1]['APP_NUMBER']);
    }

    /**
     * This ensures ordering ascending and descending works by case title APP_TITLE in draft
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_return_draft_sort_by_case_title()
    {
        $this->markTestIncomplete(
            'This test needs to write when the column DELEGATION.DEL_THREAD was added'
        );
        //Create process
        $process = factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a task
        $task = factory(Task::class)->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create application and app_delegation related with DRAFT status with a minor case title
        $application = factory(Application::class)->states('draft_minor_case')->create();
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'USR_ID' => $user->USR_ID,
        ]);
        //Create application and app_delegation related with DRAFT status with a minor case title
        $application2 = factory(Application::class)->states('draft_major_case')->create();
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'USR_ID' => $user->USR_ID,
        ]);
        // Get first page
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setOrderByColumn('APPLICATION.APP_TITLE');
        // Get first page, the minor case title
        $draft->setOrderDirection('ASC');
        $results = $draft->getData();
        $this->assertGreaterThanOrEqual($results[0]['APP_TITLE'], $results[1]['APP_TITLE']);
        // Get first page, the major case title
        $draft->setOrderDirection('DESC');
        $results = $draft->getData();
        $this->assertLessThanOrEqual($results[0]['APP_TITLE'], $results[1]['APP_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by case title PRO_TITLE in draft
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_return_draft_sort_by_process()
    {
        // Create a user
        $user = factory(User::class)->create();
        // Create some cases
        for ($i = 1; $i <= 2; $i++) {
            $process = factory(Process::class)->create();
            $task = factory(Task::class)->create([
                'PRO_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID,
            ]);
            //Create application and app_delegation related with DRAFT status
            $application = factory(Application::class)->states('draft')->create();
            factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'PRO_ID' => $process->PRO_ID,
                'TAS_ID' => $task->TAS_ID,
                'USR_ID' => $user->USR_ID,
            ]);
        }
        // Get first page
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setOrderByColumn('PRO_TITLE');
        // Get first page, the minor case title
        $draft->setOrderDirection('ASC');
        $results = $draft->getData();
        $this->assertGreaterThan($results[0]['PRO_TITLE'], $results[1]['PRO_TITLE']);
        // Get first page, the major case title
        $draft->setOrderDirection('DESC');
        $results = $draft->getData();
        $this->assertLessThan($results[0]['PRO_TITLE'], $results[1]['PRO_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by task title TAS_TITLE in draft
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_return_draft_sort_by_task_title()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a task
        for ($i = 1; $i <= 2; $i++) {
            $task = factory(Task::class)->create([
                'PRO_UID' => $process->PRO_UID,
                'TAS_TYPE' => 'NORMAL',
            ]);
            $application = factory(Application::class)->states('draft')->create();
            factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'TAS_ID' => $task->TAS_ID,
                'USR_ID' => $user->USR_ID,
            ]);
        }
        // Get first page
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setOrderByColumn('TAS_TITLE');
        // Get first page, the minor case title
        $draft->setOrderDirection('ASC');
        $results = $draft->getData();
        $this->assertGreaterThan($results[0]['TAS_TITLE'], $results[1]['TAS_TITLE']);
        // Get first page, the major case title
        $draft->setOrderDirection('DESC');
        $results = $draft->getData();
        $this->assertLessThan($results[0]['TAS_TITLE'], $results[1]['TAS_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by due date DEL_TASK_DUE_DATE in draft
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_return_draft_sort_due_date()
    {
        $faker = \Faker\Factory::create();
        //Create process
        $process = factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a task
        $task = factory(Task::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_TYPE' => 'NORMAL',
        ]);
        //Create application and app_delegation related with DRAFT status
        $application = factory(Application::class)->states('draft')->create();
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'USR_ID' => $user->USR_ID,
            'DEL_TASK_DUE_DATE' => $faker->dateTimeBetween('now', '+1 year')
        ]);
        //Create the register in delegation related to draft
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'USR_ID' => $user->USR_ID,
            'DEL_TASK_DUE_DATE' => $faker->dateTimeBetween('-2 year', '-1 year')
        ]);
        // Get first page
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setOrderByColumn('DEL_TASK_DUE_DATE');
        // Get first page, the minor case title
        $draft->setOrderDirection('ASC');
        $results = $draft->getData();
        $this->assertGreaterThan($results[0]['DEL_TASK_DUE_DATE'], $results[1]['DEL_TASK_DUE_DATE']);
        // Get first page, the major case title
        $draft->setOrderDirection('DESC');
        $results = $draft->getData();
        $this->assertLessThan($results[0]['DEL_TASK_DUE_DATE'], $results[1]['DEL_TASK_DUE_DATE']);
    }

    /**
     * This ensures searching specific cases and review the page in draft
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_search_draft_search_specific_case_uid()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a task
        $task = factory(Task::class)->create([
            'PRO_UID' => $process->PRO_UID,
        ]);
        //Create application and app_delegation related with DRAFT status
        $application = factory(Application::class)->states('draft')->create();
        factory(Delegation::class)->create([
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'USR_ID' => $user->USR_ID
        ]);
        //Create other cases
        factory(Delegation::class, 5)->create([
            'TAS_ID' => $task->TAS_ID,
            'USR_ID' => $user->USR_ID
        ]);
        // Get first page
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        // Get first page, the specific case
        $draft->setOrderDirection('ASC');
        $draft->setCaseUid($application->APP_UID);
        $results = $draft->getData();
        $this->assertEquals($application->APP_UID, $results[0]['APP_UID']);
    }

    /**
     * This ensures searching specific cases and review the page in draft
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_search_draft_search_specific_cases_uid_array()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create user
        $user = factory(User::class)->create();
        //Create a task
        $task = factory(Task::class)->create([
            'PRO_UID' => $process->PRO_UID,
        ]);
        //Create application and app_delegation related with DRAFT status
        $application = factory(Application::class)->states('draft')->create();
        factory(Delegation::class)->create([
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'USR_ID' => $user->USR_ID
        ]);
        // Get first page
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        // Get first page, the specific case
        $draft->setOrderDirection('ASC');
        $draft->setCasesUids([$application->APP_UID]);
        $results = $draft->getData();
        $this->assertEquals($application->APP_UID, $results[0]['APP_UID']);
    }

    /**
     * This ensures searching specific process and review the page in draft
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @test
     */
    public function it_should_search_draft_search_specific_process()
    {
        //Create user
        $user = factory(User::class)->create();
        for ($i = 1; $i <= 2; $i++) {
            //Create process
            $process = factory(Process::class)->create();
            $task = factory(Task::class)->create([
                'PRO_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID,
            ]);
            //Create application and app_delegation related with DRAFT status
            $application = factory(Application::class)->states('draft')->create();
            factory(Delegation::class)->create([
                'APP_NUMBER' => $application->APP_NUMBER,
                'PRO_ID' => $process->PRO_ID,
                'TAS_ID' => $task->TAS_ID,
                'USR_ID' => $user->USR_ID,
            ]);
        }
        // Get first page
        $draft = new Draft();
        $draft->setUserId($user->USR_ID);
        $draft->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        $draft->setProcessId($process->PRO_ID);
        // Get first page, the minor case title
        $draft->setOrderDirection('ASC');
        $results = $draft->getData();
        $this->assertEquals($process->PRO_TITLE, $results[0]['PRO_TITLE']);
    }
}