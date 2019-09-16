<?php
namespace Tests\unit\workflow\src\ProcessMaker\Model;

use Faker\Factory;
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

class DelegationTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * This checks to make sure pagination is working properly
     * @test
     */
    public function it_should_return_pages_of_data()
    {
        factory(User::class, 100)->create();
        factory(Process::class, 10)->create();
        factory(Delegation::class, 51)->create();
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
     * This checks to make sure pagination is working properly
     * @test
     */
    public function it_should_return_pages_of_data_unassigned()
    {
        factory(User::class, 100)->create();
        factory(Process::class, 10)->create();
        factory(Delegation::class, 50)->create();
        factory(Delegation::class)->create([
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
     * @test
     */
    public function it_should_return_process_of_data()
    {
        factory(User::class, 100)->create();
        $process = factory(Process::class)->create();
        factory(Delegation::class, 51)->create([
            'PRO_ID' => $process->id
        ]);
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, $process->id);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, $process->id);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, $process->id);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure filter by status is working properly
     * Review status filter by a specific status, such as Draft
     * @test
     */
    public function it_should_return_status_draft_of_data()
    {
        factory(User::class, 100)->create();
        factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 2001,
            'APP_STATUS_ID' => 1,
            'APP_STATUS' => 'DRAFT'
        ]);
        factory(Delegation::class, 51)->create([
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
     * @test
     */
    public function it_should_return_status_todo_of_data()
    {
        factory(User::class, 100)->create();
        factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 2001,
            'APP_STATUS_ID' => 2,
            'APP_STATUS' => 'TO_DO'
        ]);
        factory(Delegation::class, 51)->create([
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
     * @test
     */
    public function it_should_return_status_completed_of_data()
    {
        factory(User::class, 100)->create();
        factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 2001,
            'APP_STATUS_ID' => 3,
            'APP_STATUS' => 'COMPLETED',
        ]);

        factory(Delegation::class, 51)->create([
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
     * @test
     */
    public function it_should_return_status_cancelled_of_data()
    {
        factory(User::class, 100)->create();
        factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 2001,
            'APP_STATUS_ID' => 4,
            'APP_STATUS' => 'CANCELLED'
        ]);

        factory(Delegation::class, 51)->create([
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
     * @test
     */
    public function it_should_return_one_result_for_specified_user()
    {
        factory(User::class, 100)->create();
        factory(Process::class, 10)->create();
        // Create our unique user, with a unique username
        $user = factory(User::class)->create([
            'USR_USERNAME' => 'testcaseuser'
        ]);
        // Create a new delegation, but for this specific user
        factory(Delegation::class)->create([
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID
        ]);
        // Now fetch results, and assume delegation count is 1 and the user points to our user
        $results = Delegation::search($user->USR_ID);
        $this->assertCount(1, $results['data']);
        $this->assertEquals('testcaseuser', $results['data'][0]['USRCR_USR_USERNAME']);
    }

    /**
     * This ensures searching by case number and review the order
     * @test
     */
    public function it_should_search_by_case_id_and_order_of_data()
    {
        factory(User::class, 100)->create();
        factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 11
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 111
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 1111
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 11111
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 111111
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 1111111
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 11111111
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        // Searching by a existent case number, result ordered in DESC mode
        $results = Delegation::search(null, 0, 10, 11, null, null, 'DESC',
            'APP_NUMBER', null, null, null, 'APP_NUMBER');
        $this->assertCount(1, $results['data']);
        $this->assertEquals(11, $results['data'][0]['APP_NUMBER']);
        // Searching by another existent case number, result ordered in ASC mode
        $results = Delegation::search(null, 0, 10, 11111, null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_NUMBER');
        $this->assertCount(1, $results['data']);
        $this->assertEquals(11111, $results['data'][0]['APP_NUMBER']);
        // Searching by another existent case number, result ordered in DESC mode
        $results = Delegation::search(null, 0, 10, 1111111, null, null, 'DESC',
            'APP_NUMBER', null, null, null, 'APP_NUMBER');
        $this->assertCount(1, $results['data']);
        $this->assertEquals(1111111, $results['data'][0]['APP_NUMBER']);
        // Searching by a not existent case number, result ordered in DESC mode
        $results = Delegation::search(null, 0, 10, 1000, null, null, 'DESC',
            'APP_NUMBER', null, null, null, 'APP_NUMBER');
        $this->assertCount(0, $results['data']);
        // Searching by a not existent case number, result ordered in ASC mode
        $results = Delegation::search(null, 0, 10, 99999, null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_NUMBER');
        $this->assertCount(0, $results['data']);
    }

    /**
     * This ensures searching by case title and review the page
     * @test
     */
    public function it_should_search_by_case_title_and_pages_of_data_app_number_matches_case_title()
    {
        factory(User::class, 100)->create();
        factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 3001,
            'APP_TITLE' => 'Request # 3001'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 3010,
            'APP_TITLE' => 'Request # 3010'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 3011,
            'APP_TITLE' => 'Request # 3011'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 3012,
            'APP_TITLE' => 'Request # 3012'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 3013,
            'APP_TITLE' => 'Request # 3013'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 3014,
            'APP_TITLE' => 'Request # 3014'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);

        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();

        // Get first page, the major case id
        $results = Delegation::search(null, 0, 10, 'Request', null, null, 'DESC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(6, $results['data']);
        $this->assertEquals(3014, $results['data'][0]['APP_NUMBER']);
        $this->assertEquals('Request # 3014', $results['data'][0]['APP_TITLE']);

        // Get first page, the minor case id
        $results = Delegation::search(null, 0, 10, 'Request', null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(6, $results['data']);
        $this->assertEquals(3001, $results['data'][0]['APP_NUMBER']);
        $this->assertEquals('Request # 3001', $results['data'][0]['APP_TITLE']);

        // Check the pagination
        $results = Delegation::search(null, 0, 5, 'Request', null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(5, $results['data']);
        $results = Delegation::search(null, 5, 2, 'Request', null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(1, $results['data']);

        // We need to clean the tables manually
        // @todo: The "Delegation" factory should be improved, the create method always is creating a record in application table
        DB::unprepared("TRUNCATE APPLICATION;");
        DB::unprepared("TRUNCATE APP_DELEGATION;");
    }

    /**
     * This ensures searching by task title and review the page
     * @test
     */
    public function it_should_search_by_task_title_and_pages_of_data()
    {
        factory(User::class, 100)->create();
        factory(Process::class)->create();
        $task = factory(Task::class)->create([
            'TAS_ID' => 1,
            'TAS_TITLE' => 'Request task'
        ]);
        factory(Delegation::class, 5)->create([
            'TAS_ID' => $task->TAS_ID
        ]);
        $task = factory(Task::class)->create([
            'TAS_ID' => 2,
            'TAS_TITLE' => 'Account task'
        ]);
        factory(Delegation::class, 5)->create([
            'TAS_ID' => $task->TAS_ID
        ]);
        // Get first page, the order taskTitle
        $results = Delegation::search(null, 0, 6, 'task', null, null, 'ASC',
            'TAS_TITLE', null, null, null, 'TAS_TITLE');
        $this->assertCount(6, $results['data']);
        $this->assertEquals('Account task', $results['data'][0]['APP_TAS_TITLE']);
        $results = Delegation::search(null, 6, 6, 'task', null, null, 'ASC',
            'TAS_TITLE', null, null, null, 'TAS_TITLE');
        $this->assertEquals('Request task', $results['data'][0]['APP_TAS_TITLE']);

        // Get first page, the order taskTitle
        $results = Delegation::search(null, 0, 6, 'task', null, null, 'DESC',
            'TAS_TITLE', null, null, null, 'TAS_TITLE');
        $this->assertCount(6, $results['data']);
        $this->assertEquals('Request task', $results['data'][0]['APP_TAS_TITLE']);
        $results = Delegation::search(null, 6, 6, 'task', null, null, 'DESC',
            'TAS_TITLE', null, null, null, 'TAS_TITLE');
        $this->assertEquals('Account task', $results['data'][0]['APP_TAS_TITLE']);
        //Check the pagination
        $results = Delegation::search(null, 0, 6, 'task', null, null, 'DESC',
            'TAS_TITLE', null, null, null, 'TAS_TITLE');
        $this->assertCount(6, $results['data']);
        $results = Delegation::search(null, 6, 6, 'task', null, null, 'DESC',
            'TAS_TITLE', null, null, null, 'TAS_TITLE');
        $this->assertCount(4, $results['data']);
    }

    /**
     * This ensures searching by case title and review the page
     * @test
     */
    public function it_should_search_by_case_title_and_pages_of_data_app_number_no_matches_case_title()
    {
        factory(User::class, 100)->create();
        factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 2001,
            'APP_TITLE' => 'Request from Abigail check nro 25001'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 2010,
            'APP_TITLE' => 'Request from Abigail check nro 12'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 2011,
            'APP_TITLE' => 'Request from Abigail check nro 1000'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 2012,
            'APP_TITLE' => 'Request from Abigail check nro 11000'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2013,
            'APP_TITLE' => 'Request from Abigail check nro 12000'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_TITLE' => 2014,
            'APP_TITLE' => 'Request from Abigail check nro 111'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);

        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();

        // Get first page, the major case title
        $results = Delegation::search(null, 0, 10, 'Abigail', null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(6, $results['data']);
        $this->assertEquals(2001, $results['data'][0]['APP_NUMBER']);
        $this->assertEquals('Request from Abigail check nro 25001', $results['data'][0]['APP_TITLE']);

        // Check the pagination
        $results = Delegation::search(null, 0, 5, 'Abigail', null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(5, $results['data']);
        $results = Delegation::search(null, 5, 2, 'Abigail', null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(1, $results['data']);

        // We need to clean the tables manually
        // @todo: The "Delegation" factory should be improved, the create method always is creating a record in application table
        DB::unprepared("TRUNCATE APPLICATION;");
        DB::unprepared("TRUNCATE APP_DELEGATION;");
    }

    /**
     * This ensures ordering ascending and descending works by case number APP_NUMBER
     * @test
     */
    public function it_should_sort_by_case_id()
    {
        factory(User::class, 100)->create();
        factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 2001
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 30002
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        // Get first page, the minor case id
        $results = Delegation::search(null, 0, 25, null, null, null, 'ASC', 'APP_NUMBER');
        $this->assertCount(2, $results['data']);
        $this->assertEquals(2001, $results['data'][0]['APP_NUMBER']);
        $this->assertEquals(30002, $results['data'][1]['APP_NUMBER']);
        // Get first page, the major case id
        $results = Delegation::search(null, 0, 25, null, null, null, 'DESC', 'APP_NUMBER');
        $this->assertCount(2, $results['data']);
        $this->assertEquals(30002, $results['data'][0]['APP_NUMBER']);
        $this->assertEquals(2001, $results['data'][1]['APP_NUMBER']);
    }

    /**
     * This ensures ordering ascending and descending works by case title APP_TITLE
     * @test
     */
    public function it_should_sort_by_case_title()
    {
        factory(User::class, 100)->create();
        factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 2001,
            'APP_TITLE' => 'Request by Thomas'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_NUMBER' => 30002,
            'APP_TITLE' => 'Request by Ariel'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        // Get first page, the minor case id
        $results = Delegation::search(null, 0, 25, null, null, null, 'ASC', 'APP_TITLE');
        $this->assertCount(2, $results['data']);
        $this->assertEquals('Request by Ariel', $results['data'][0]['APP_TITLE']);
        $this->assertEquals('Request by Thomas', $results['data'][1]['APP_TITLE']);
        // Get first page, the major case id
        $results = Delegation::search(null, 0, 25, null, null, null, 'DESC', 'APP_TITLE');
        $this->assertCount(2, $results['data']);
        $this->assertEquals('Request by Thomas', $results['data'][0]['APP_TITLE']);
        $this->assertEquals('Request by Ariel', $results['data'][1]['APP_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by case title APP_PRO_TITLE
     * @test
     */
    public function it_should_sort_by_process()
    {
        $faker = Factory::create();
        factory(User::class, 100)->create();
        $process = factory(Process::class)->create([
            'PRO_ID' => $faker->unique()->numberBetween(1, 10000000),
            'PRO_TITLE' => 'Egypt Supplier Payment Proposal'
        ]);
        factory(Delegation::class)->create([
            'PRO_ID' => $process->id
        ]);
        $process = factory(Process::class)->create([
            'PRO_ID' => $faker->unique()->numberBetween(1, 10000000),
            'PRO_TITLE' => 'China Supplier Payment Proposal'
        ]);
        factory(Delegation::class)->create([
            'PRO_ID' => $process->id
        ]);
        $process = factory(Process::class)->create([
            'PRO_ID' => $faker->unique()->numberBetween(1, 10000000),
            'PRO_TITLE' => 'Russia Supplier Payment Proposal'
        ]);
        factory(Delegation::class)->create([
            'PRO_ID' => $process->id
        ]);
        // Get first page, all process ordering ascending
        $results = Delegation::search(null, 0, 3, null, null, null, 'ASC', 'APP_PRO_TITLE');
        $this->assertCount(3, $results['data']);
        $this->assertEquals('China Supplier Payment Proposal', $results['data'][0]['APP_PRO_TITLE']);
        $this->assertEquals('Egypt Supplier Payment Proposal', $results['data'][1]['APP_PRO_TITLE']);
        $this->assertEquals('Russia Supplier Payment Proposal', $results['data'][2]['APP_PRO_TITLE']);
        // Get first page, all process ordering descending
        $results = Delegation::search(null, 0, 3, null, null, null, 'DESC', 'APP_PRO_TITLE');
        $this->assertCount(3, $results['data']);
        $this->assertEquals('Russia Supplier Payment Proposal', $results['data'][0]['APP_PRO_TITLE']);
        $this->assertEquals('Egypt Supplier Payment Proposal', $results['data'][1]['APP_PRO_TITLE']);
        $this->assertEquals('China Supplier Payment Proposal', $results['data'][2]['APP_PRO_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by task title APP_TAS_TITLE
     * @test
     */
    public function it_should_sort_by_task_title()
    {
        factory(User::class, 100)->create();
        factory(Process::class)->create();
        $task = factory(Task::class)->create([
            'TAS_ID' => 1000,
            'TAS_TITLE' => 'Initiate Request'
        ]);
        factory(Delegation::class)->create([
            'TAS_ID' => $task->TAS_ID
        ]);
        $task = factory(Task::class)->create([
            'TAS_ID' => 4000,
            'TAS_TITLE' => 'Waiting for AP Manager Validation'
        ]);
        factory(Delegation::class)->create([
            'TAS_ID' => $task->TAS_ID
        ]);

        $results = Delegation::search(null, 0, 25, null, null, null, 'ASC', 'APP_TAS_TITLE');
        $this->assertCount(2, $results['data']);
        $this->assertEquals('Initiate Request', $results['data'][0]['APP_TAS_TITLE']);
        $this->assertEquals('Waiting for AP Manager Validation', $results['data'][1]['APP_TAS_TITLE']);

        $results = Delegation::search(null, 0, 25, null, null, null, 'DESC', 'APP_TAS_TITLE');
        $this->assertCount(2, $results['data']);
        $this->assertEquals('Waiting for AP Manager Validation', $results['data'][0]['APP_TAS_TITLE']);
        $this->assertEquals('Initiate Request', $results['data'][1]['APP_TAS_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by current user
     * @test
     */
    public function it_should_sort_by_user()
    {
        factory(User::class, 100)->create();
        factory(Process::class, 10)->create();
        // Create our unique user, with a unique username
        $user = factory(User::class)->create([
            'USR_USERNAME' => 'gary',
            'USR_LASTNAME' => 'Gary',
            'USR_FIRSTNAME' => 'Bailey',
        ]);
        // Create a new delegation, but for this specific user
        factory(Delegation::class)->create([
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID
        ]);
        $user = factory(User::class)->create([
            'USR_USERNAME' => 'paul',
            'USR_LASTNAME' => 'Paul',
            'USR_FIRSTNAME' => 'Griffis',
        ]);
        // Create a new delegation, but for this specific user
        factory(Delegation::class)->create([
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID
        ]);
        // Now fetch results, and assume delegation count is 2 and the ordering ascending return Gary
        $results = Delegation::search(null, 0, 25, null, null, null, 'ASC', 'APP_CURRENT_USER');
        $this->assertCount(2, $results['data']);
        $this->assertEquals('Gary Bailey', $results['data'][0]['APP_CURRENT_USER']);

        // Now fetch results, and assume delegation count is 2 and the ordering descending return Gary
        $results = Delegation::search(null, 0, 25, null, null, null, 'DESC', 'APP_CURRENT_USER');
        $this->assertCount(2, $results['data']);
        $this->assertEquals('Paul Griffis', $results['data'][0]['APP_CURRENT_USER']);
    }

    /**
     * This ensures ordering ordering ascending and descending works by last modified APP_UPDATE_DATE
     * @test
     */
    public function it_should_sort_by_last_modified()
    {
        factory(User::class,100)->create();
        factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_UPDATE_DATE' => '2019-01-02 00:00:00'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_UPDATE_DATE' => '2019-01-03 00:00:00'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_UPDATE_DATE' => '2019-01-04 00:00:00'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        // Get first page, the minor last modified
        $results = Delegation::search(null, 0, 1, null, null, null, 'ASC', 'APP_UPDATE_DATE');
        $this->assertCount(1, $results['data']);
        $this->assertEquals('2019-01-02 00:00:00', $results['data'][0]['APP_UPDATE_DATE']);

        $results = Delegation::search(null, 1, 1, null, null, null, 'ASC', 'APP_UPDATE_DATE');
        $this->assertCount(1, $results['data']);
        $this->assertEquals('2019-01-03 00:00:00', $results['data'][0]['APP_UPDATE_DATE']);

        $results = Delegation::search(null, 2, 1, null, null, null, 'ASC', 'APP_UPDATE_DATE');
        $this->assertCount(1, $results['data']);
        $this->assertEquals('2019-01-04 00:00:00', $results['data'][0]['APP_UPDATE_DATE']);

        $results = Delegation::search(null, 0, 1, null, null, null, 'DESC', 'APP_UPDATE_DATE');
        $this->assertCount(1, $results['data']);
        $this->assertEquals('2019-01-04 00:00:00', $results['data'][0]['APP_UPDATE_DATE']);

        $results = Delegation::search(null, 1, 1, null, null, null, 'DESC', 'APP_UPDATE_DATE');
        $this->assertCount(1, $results['data']);
        $this->assertEquals('2019-01-03 00:00:00', $results['data'][0]['APP_UPDATE_DATE']);

        $results = Delegation::search(null, 2, 1, null, null, null, 'DESC', 'APP_UPDATE_DATE');
        $this->assertCount(1, $results['data']);
        $this->assertEquals('2019-01-02 00:00:00', $results['data'][0]['APP_UPDATE_DATE']);
    }

    /**
     * This ensures ordering ascending and descending works by due date DEL_TASK_DUE_DATE
     * @test
     */
    public function it_should_sort_by_due_date()
    {
        factory(User::class,100)->create();
        factory(Process::class)->create();
        factory(Delegation::class, 10)->create([
            'DEL_TASK_DUE_DATE' => '2019-01-02 00:00:00'
        ]);
        factory(Delegation::class, 10)->create([
            'DEL_TASK_DUE_DATE' => '2019-01-03 00:00:00'
        ]);
        factory(Delegation::class, 9)->create([
            'DEL_TASK_DUE_DATE' => '2019-01-04 00:00:00'
        ]);
        // Get first page, the minor last modified
        $results = Delegation::search(null, 0, 10, null, null, null, 'ASC', 'DEL_TASK_DUE_DATE');
        $this->assertCount(10, $results['data']);
        $this->assertEquals('2019-01-02 00:00:00', $results['data'][0]['DEL_TASK_DUE_DATE']);

        $results = Delegation::search(null, 10, 10, null, null, null, 'ASC', 'DEL_TASK_DUE_DATE');
        $this->assertCount(10, $results['data']);
        $this->assertEquals('2019-01-03 00:00:00', $results['data'][0]['DEL_TASK_DUE_DATE']);

        $results = Delegation::search(null, 20, 10, null, null, null, 'ASC', 'DEL_TASK_DUE_DATE');
        $this->assertCount(9, $results['data']);
        $this->assertEquals('2019-01-04 00:00:00', $results['data'][0]['DEL_TASK_DUE_DATE']);

        $results = Delegation::search(null, 0, 10, null, null, null, 'DESC', 'DEL_TASK_DUE_DATE');
        $this->assertCount(10, $results['data']);
        $this->assertEquals('2019-01-04 00:00:00', $results['data'][0]['DEL_TASK_DUE_DATE']);

        $results = Delegation::search(null, 10, 10, null, null, null, 'DESC', 'DEL_TASK_DUE_DATE');
        $this->assertCount(10, $results['data']);
        $this->assertEquals('2019-01-03 00:00:00', $results['data'][0]['DEL_TASK_DUE_DATE']);

        $results = Delegation::search(null, 20, 10, null, null, null, 'DESC', 'DEL_TASK_DUE_DATE');
        $this->assertCount(9, $results['data']);
        $this->assertEquals('2019-01-02 00:00:00', $results['data'][0]['DEL_TASK_DUE_DATE']);
    }

    /**
     * This ensures ordering ascending and descending works by status APP_STATUS
     * @test
     */
    public function it_should_sort_by_status()
    {
        factory(User::class,100)->create();
        factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_STATUS' => 'DRAFT'
        ]);
        factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_STATUS' => 'TO_DO'
        ]);
        factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_STATUS' => 'COMPLETED'
        ]);
        factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        $application = factory(Application::class)->create([
            'APP_STATUS' => 'CANCELLED'
        ]);
        factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);

        // Get first page, the minor status label
        $results = Delegation::search(null, 0, 25, null, null, null, 'ASC', 'APP_STATUS');
        $this->assertEquals('CANCELLED', $results['data'][0]['APP_STATUS']);
        $results = Delegation::search(null, 25, 25, null, null, null, 'ASC', 'APP_STATUS');
        $this->assertEquals('COMPLETED', $results['data'][0]['APP_STATUS']);
        $results = Delegation::search(null, 50, 25, null, null, null, 'ASC', 'APP_STATUS');
        $this->assertEquals('DRAFT', $results['data'][0]['APP_STATUS']);
        $results = Delegation::search(null, 75, 25, null, null, null, 'ASC', 'APP_STATUS');
        $this->assertEquals('TO_DO', $results['data'][0]['APP_STATUS']);
        // Get first page, the major status label
        $results = Delegation::search(null, 0, 25, null, null, null, 'DESC', 'APP_STATUS');
        $this->assertEquals('TO_DO', $results['data'][0]['APP_STATUS']);
        $results = Delegation::search(null, 25, 25, null, null, null, 'DESC', 'APP_STATUS');
        $this->assertEquals('DRAFT', $results['data'][0]['APP_STATUS']);
        $results = Delegation::search(null, 50, 25, null, null, null, 'DESC', 'APP_STATUS');
        $this->assertEquals('COMPLETED', $results['data'][0]['APP_STATUS']);
        $results = Delegation::search(null, 75, 25, null, null, null, 'DESC', 'APP_STATUS');
        $this->assertEquals('CANCELLED', $results['data'][0]['APP_STATUS']);
    }

    /**
     * This checks to make sure filter by category is working properly
     * @test
     */
    public function it_should_return_data_filtered_by_process_category()
    {
        factory(User::class, 100)->create();
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
        $processCategorySearch = factory(ProcessCategory::class, 1)->create();
        $categoryUid = $processCategorySearch[0]->CATEGORY_UID;
        $processSearch = factory(Process::class, 1)->create([
            'PRO_ID' => 5,
            'PRO_CATEGORY' => $categoryUid
        ]);
        // Delegations to found
        factory(Delegation::class, 51)->create([
            'PRO_ID' => $processSearch[0]->id
        ]);

        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, null, null, null, null, $categoryUid);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, null, null, null, null, $categoryUid);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, null, null, null, null, $categoryUid);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This ensure the result is right when you search between two given dates
     * @test
     */
    public function it_should_return_right_data_between_two_dates()
    {
        factory(User::class, 10)->create();
        factory(Process::class, 10)->create();
        factory(Delegation::class, 10)->create(['DEL_DELEGATE_DATE' => '2019-01-02 00:00:00']);
        factory(Delegation::class, 10)->create(['DEL_DELEGATE_DATE' => '2019-01-03 00:00:00']);
        factory(Delegation::class, 10)->create(['DEL_DELEGATE_DATE' => '2019-01-04 00:00:00']);
        factory(Delegation::class, 10)->create(['DEL_DELEGATE_DATE' => '2019-01-05 00:00:00']);
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
     * @test
     */
    public function it_should_return_right_data_when_you_send_only_dateFrom_parameter()
    {
        factory(User::class, 10)->create();
        factory(Process::class, 10)->create();
        factory(Delegation::class, 10)->create(['DEL_DELEGATE_DATE' => '2019-01-02 00:00:00']);
        factory(Delegation::class, 10)->create(['DEL_DELEGATE_DATE' => '2019-01-03 00:00:00']);
        factory(Delegation::class, 10)->create(['DEL_DELEGATE_DATE' => '2019-01-04 00:00:00']);
        factory(Delegation::class, 10)->create(['DEL_DELEGATE_DATE' => '2019-01-05 00:00:00']);
        $results = Delegation::search(null, 0, 50, null, null, null, null, null, null, '2019-01-02 00:00:00',
            null);
        $this->assertCount(40, $results['data']);
        foreach ($results['data'] as $value) {
            $this->assertGreaterThanOrEqual('2019-01-02 00:00:00', $value['DEL_DELEGATE_DATE']);
            $this->assertRegExp('(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) ', $value['DEL_DELEGATE_DATE']);
        }
    }

    /**
     * This ensure the result is right when you search to a given date
     * @test
     */
    public function it_should_return_right_data_when_you_send_only_dateTo_parameter()
    {
        factory(User::class, 10)->create();
        factory(Process::class, 10)->create();
        factory(Delegation::class, 10)->create(['DEL_DELEGATE_DATE' => '2019-01-02 00:00:00']);
        factory(Delegation::class, 10)->create(['DEL_DELEGATE_DATE' => '2019-01-03 00:00:00']);
        factory(Delegation::class, 10)->create(['DEL_DELEGATE_DATE' => '2019-01-04 00:00:00']);
        factory(Delegation::class, 10)->create(['DEL_DELEGATE_DATE' => '2019-01-05 00:00:00']);
        $results = Delegation::search(null, 0, 50, null, null, null, null, null, null, null,
            '2019-01-04 00:00:00');
        $this->assertCount(30, $results['data']);
        foreach ($results['data'] as $value) {
            $this->assertLessThanOrEqual('2019-01-04 00:00:00', $value['DEL_DELEGATE_DATE']);
            $this->assertRegExp('(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) ', $value['DEL_DELEGATE_DATE']);
        }
    }

    /**
     * This ensures return the correct data by sequential
     * @test
     */
    public function it_should_return_by_sequential_tasks_pages_of_data()
    {
        factory(User::class)->create();
        // Create a process
        $process = factory(Process::class)->create();
        $application = factory(Application::class)->create();
        // Create task
        $taskNormal = factory(Task::class)->create([
            'TAS_TYPE' => 'NORMAL'
        ]);
        // Create a thread with the user, process and application defined before
        factory(Delegation::class)->create([
            'PRO_ID' => $process->id,
            'TAS_ID' => $taskNormal->TAS_ID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'DEL_INDEX' => 1
        ]);
        // Define a dummy task
        $taskDummy = factory(Task::class)->create([
            'TAS_TYPE' => 'INTERMEDIATE-THROW'
        ]);
        // Create a thread with the dummy task this does not need a user
        factory(Delegation::class)->create([
            'PRO_ID' => $process->id,
            'USR_ID' => 0,
            'TAS_ID' => $taskDummy->TAS_ID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2
        ]);
        // Create a thread with the user, process and application defined before
        $res = factory(Delegation::class)->create([
            'PRO_ID' => $process->id,
            'TAS_ID' => $taskNormal->TAS_ID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 3
        ]);
        // Review if the thread OPEN is showed
        $results = Delegation::search(null, 0, 10);
        $this->assertCount(1, $results['data']);
        $this->assertEquals('OPEN', $results['data'][0]['DEL_THREAD_STATUS']);
    }

    /**
     * This ensures return the correct data by parallel task all threads CLOSED
     * @test
     */
    public function it_should_return_by_parallel_tasks_threads_closed()
    {
        factory(User::class, 100)->create();
        factory(Process::class)->create();
        $task = factory(Task::class)->create([
            'TAS_TITLE' => 'Parallel task 1'
        ]);
        factory(Delegation::class, 5)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED'
        ]);
        $task = factory(Task::class)->create([
            'TAS_TITLE' => 'Parallel task 2'
        ]);
        factory(Delegation::class, 5)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED'
        ]);
        // Get first page, the order taskTitle
        $results = Delegation::search(null, 0, 2, null, null, null, 'ASC',
            'TAS_TITLE', null, null, null, 'TAS_TITLE');
        $this->assertCount(0, $results['data']);

        // Get first page, the order taskTitle
        $results = Delegation::search(null, 0, 2, null, null, null, 'DESC',
            'TAS_TITLE', null, null, null, 'TAS_TITLE');
        $this->assertCount(0, $results['data']);
    }

    /**
     * This ensures return the correct data by parallel task all threads OPEN
     * @test
     */
    public function it_should_return_by_parallel_tasks_threads_open()
    {
        factory(User::class, 100)->create();
        factory(Process::class, 1)->create();
        //Create the threads
        factory(Delegation::class, 5)->create([
            'DEL_THREAD_STATUS' => 'OPEN'
        ]);
        // Get first page, all the open status
        $results = Delegation::search(null, 0, 5, null, null, null);
        $this->assertCount(5, $results['data']);
        $this->assertEquals('OPEN', $results['data'][0]['DEL_THREAD_STATUS']);
        $this->assertEquals('OPEN', $results['data'][4]['DEL_THREAD_STATUS']);
    }

    /**
     * Review when the status is empty
     * @test
     */
    public function it_should_return_status_empty()
    {
        factory(User::class, 100)->create();
        factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_STATUS' => ''
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER
        ]);
        // Review the filter by status empty
        $results = Delegation::search(null, 0, 25);
        $this->assertEmpty($results['data'][0]['APP_STATUS']);
    }

    /**
     * Review when filter when the process and category does not have a relation
     * @test
     */
    public function it_should_return_process_and_category_does_not_have_a_relation()
    {
        //Create a category
        $category = factory(ProcessCategory::class, 2)->create();
        //Define a process with category
        $processWithCat = factory(Process::class)->create([
            'PRO_CATEGORY' => $category[0]->CATEGORY_UID
        ]);
        //Define a process without category
        $processNoCategory = factory(Process::class)->create([
            'PRO_CATEGORY' => ''
        ]);
        //Create a delegation related with the process with category
        factory(Delegation::class)->create([
            'PRO_ID' => $processWithCat->id
        ]);
        //Create a delegation related with the process without category
        factory(Delegation::class)->create([
            'PRO_ID' => $processNoCategory->id
        ]);
        //Search the cases related to the category and process does not have relation
        $results = Delegation::search(null, 0, 25, null, $processWithCat->id, null, null, null, $category[1]->CATEGORY_UID);
        $this->assertCount(0, $results['data']);
        //Search the cases related to the category and process does not have relation
        $results = Delegation::search(null, 0, 25, null, $processNoCategory->id, null, null, null, $category[0]->CATEGORY_UID);
        $this->assertCount(0, $results['data']);
    }

    /**
     * Review when filter when the process and category does have a relation
     * @test
     */
    public function it_should_return_process_and_category_does_have_a_relation()
    {
        //Create a category
        $category = factory(ProcessCategory::class)->create();
        //Define a process related with he previous category
        $processWithCat = factory(Process::class)->create([
            'PRO_CATEGORY' => $category->CATEGORY_UID
        ]);
        //Create a delegation related to this process
        factory(Delegation::class)->create([
            'PRO_ID' => $processWithCat->id
        ]);
        //Define a process related with he previous category
        $process = factory(Process::class)->create([
            'PRO_CATEGORY' => ''
        ]);
        //Create a delegation related to other process
        factory(Delegation::class, 5)->create([
            'PRO_ID' => $process->id,
        ]);
        //Search the cases when the category and process does have relation with a category
        $results = Delegation::search(null, 0, 25, null, $processWithCat->id, null, null, null, $category->CATEGORY_UID);
        $this->assertCount(1, $results['data']);
        //Search the cases when the category and process does have relation with category empty
        $results = Delegation::search(null, 0, 25, null, $process->id, null, null, null, '');
        $this->assertCount(5, $results['data']);
    }

    /**
     * Check if return participation information
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
     * This checks the counters is working properly in self-service user assigned
     * @covers Delegation::countSelfService
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
     * This checks the counters is working properly in self-service-value-based when the variable has a value related with the USR_UID
     * When the value assigned in the variable @@ARRAY_OF_USERS = [USR_UID]
     * @covers Delegation::countSelfService
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
     * This checks the counters is working properly in self-service and self-service value based
     * @covers Delegation::countSelfService
     * @test
     */
    public function it_should_count_cases_by_user_with_self_service_mixed_with_self_service_value_based()
    {
        //Create process
        $process = factory(Process::class)->create();
        //Create a case
        $application = factory(Application::class)->create();
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
        //Create the register in self service
        factory(Delegation::class, 15)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Create a task self service value based
        $task1 = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '@@ARRAY_OF_USERS',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Create the relation for the value assigned in the TAS_GROUP_VARIABLE
        $appSelfValue = factory(AppAssignSelfServiceValue::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 2,
            'TAS_ID' => $task1->TAS_ID
        ]);
        factory(AppAssignSelfServiceValueGroup::class)->create([
            'ID' => $appSelfValue->ID,
            'GRP_UID' => $user->USR_UID,
            'ASSIGNEE_ID' => $user->USR_ID, //The usrId or grpId
            'ASSIGNEE_TYPE' => 1 //Related to the user=1 related to the group=2
        ]);
        //Create the register in self service value based
        factory(Delegation::class, 15)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => $appSelfValue->DEL_INDEX,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        //Review the count self-service
        $result = Delegation::countSelfService($user->USR_UID);
        $this->assertEquals(30, $result);
    }

    /**
     * This checks the counters is working properly in self-service group assigned
     * @covers Delegation::countSelfService
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
     * This checks the counters is working properly in self-service-value-based when the variable has a value related with the GRP_UID
     * When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID]
     * @covers Delegation::countSelfService
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
     * This checks the counters is working properly in self-service user and group assigned in parallel task
     * @covers Delegation::countSelfService
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
     * This checks the counters is working properly in self-service-value-based with GRP_UID and USR_UID in parallel task
     * When the value assigned in the variable @@ARRAY_OF_USERS = [GRP_UID, USR_UID]
     * @covers Delegation::countSelfService
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
     * @covers Delegation::getCurrentUser
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
     * @covers Delegation::getCurrentUser
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
     * @covers Delegation::getCurrentUser
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
     * @covers Delegation::getOpenThreads
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
     * @covers Delegation::getOpenThreads
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
     * @covers Delegation::getOpenThreads
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
}