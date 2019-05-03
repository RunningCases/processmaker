<?php
namespace Tests\unit\workflow\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessCategory;
use ProcessMaker\Model\Task;
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
        factory(Delegation::class, 1)->create([
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
        $process = factory(Process::class, 1)->create([
            'PRO_ID' => 1
        ]);
        factory(Delegation::class, 51)->create([
            'PRO_ID' => $process[0]->id
        ]);
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, $process[0]->id);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, $process[0]->id);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, $process[0]->id);
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
        factory(Process::class, 1)->create();
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2001,
            'APP_STATUS_ID' => 1,
            'APP_STATUS' => 'DRAFT'
        ]);
        factory(Delegation::class, 51)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        // Review the filter by status DRAFT
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, null, $application[0]->APP_STATUS_ID);
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
        factory(Process::class, 1)->create();
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2001,
            'APP_STATUS_ID' => 2,
            'APP_STATUS' => 'TO_DO'
        ]);
        factory(Delegation::class, 51)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        // Review the filter by status TO_DO
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, null, $application[0]->APP_STATUS_ID);
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
        factory(Process::class, 1)->create();
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2001,
            'APP_STATUS_ID' => 3,
            'APP_STATUS' => 'COMPLETED',
        ]);

        factory(Delegation::class, 51)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'DEL_LAST_INDEX' => 1
        ]);
        // Review the filter by status COMPLETED
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, null, $application[0]->APP_STATUS_ID);
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
        factory(Process::class, 1)->create();
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2001,
            'APP_STATUS_ID' => 4,
            'APP_STATUS' => 'CANCELLED'
        ]);

        factory(Delegation::class, 51)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'DEL_LAST_INDEX' => 1
        ]);
        // Review the filter by status CANCELLED
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, null, null, $application[0]->APP_STATUS_ID);
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
            'USR_ID' => $user->id
        ]);
        // Now fetch results, and assume delegation count is 1 and the user points to our user
        $results = Delegation::search($user->id);
        $this->assertCount(1, $results['data']);
        $this->assertEquals('testcaseuser', $results['data'][0]['USRCR_USR_USERNAME']);
    }

    /**
     * This ensures searching by case number and review the page
     * @test
     */
    public function it_should_search_by_case_id_and_pages_of_data()
    {
        factory(User::class, 100)->create();
        factory(Process::class, 1)->create();
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2001
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2010
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2011
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2012
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2013
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2014
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2015
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        // Get first page, the major case id
        $results = Delegation::search(null, 0, 10, 1, null, null, 'DESC',
            'APP_NUMBER', null, null, null, 'APP_NUMBER');
        $this->assertCount(7, $results['data']);
        $this->assertEquals(2015, $results['data'][0]['APP_NUMBER']);
        // Get first page, the minor case id
        $results = Delegation::search(null, 0, 10, 1, null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_NUMBER');
        $this->assertCount(7, $results['data']);
        $this->assertEquals(2001, $results['data'][0]['APP_NUMBER']);
        //Check the pagination
        $results = Delegation::search(null, 0, 5, 1, null, null, 'DESC',
            'APP_NUMBER', null, null, null, 'APP_NUMBER');
        $this->assertCount(5, $results['data']);
        $results = Delegation::search(null, 5, 2, 1, null, null, 'DESC',
            'APP_NUMBER', null, null, null, 'APP_NUMBER');
        $this->assertCount(2, $results['data']);
    }

    /**
     * This ensures searching by case title and review the page
     * case title contain the case number, ex: APP_TITLE = 'Request # @=APP_NUMBER'
     * @test
     */
    public function it_should_search_by_case_title_and_pages_of_data_app_number_matches_case_title()
    {
        factory(User::class, 100)->create();
        factory(Process::class, 1)->create();
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 3001,
            'APP_TITLE' => 'Request # 3001'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 3010,
            'APP_TITLE' => 'Request # 3010'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 3011,
            'APP_TITLE' => 'Request # 3011'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 3012,
            'APP_TITLE' => 'Request # 3012'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 3013,
            'APP_TITLE' => 'Request # 3013'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_TITLE' => 3014,
            'APP_TITLE' => 'Request # 3014'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);

        // Get first page, the major case id
        $results = Delegation::search(null, 0, 10, '1', null, null, 'DESC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(6, $results['data']);
        $this->assertEquals('Request # 3014', $results['data'][0]['APP_TITLE']);

        // Get first page, the minor case id
        $results = Delegation::search(null, 0, 10, '1', null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(6, $results['data']);
        $this->assertEquals(3001, $results['data'][0]['APP_NUMBER']);
        $this->assertEquals('Request # 3001', $results['data'][0]['APP_TITLE']);
        //Check the pagination
        $results = Delegation::search(null, 0, 5, '1', null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(5, $results['data']);
        $results = Delegation::search(null, 5, 2, '1', null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(1, $results['data']);
    }

    /**
     * This ensures searching by task title and review the page
     * @test
     */
    public function it_should_search_by_task_title_and_pages_of_data()
    {
        factory(User::class, 100)->create();
        factory(Process::class, 1)->create();
        $task = factory(Task::class, 1)->create([
            'TAS_ID' => 1,
            'TAS_TITLE' => 'Request task'
        ]);
        factory(Delegation::class, 5)->create([
            'TAS_ID' => $task[0]->TAS_ID
        ]);
        $task = factory(Task::class, 1)->create([
            'TAS_ID' => 2,
            'TAS_TITLE' => 'Account task'
        ]);
        factory(Delegation::class, 5)->create([
            'TAS_ID' => $task[0]->TAS_ID
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
     * case title does not match with case number (hertland use case)
     * @test
     */
    public function it_should_search_by_case_title_and_pages_of_data_app_number_no_matches_case_title()
    {
        factory(User::class, 100)->create();
        factory(Process::class, 1)->create();
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2001,
            'APP_TITLE' => 'Request from Abigail check nro 25001'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2010,
            'APP_TITLE' => 'Request from Abigail check nro 12'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2011,
            'APP_TITLE' => 'Request from Abigail check nro 1000'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2012,
            'APP_TITLE' => 'Request from Abigail check nro 11000'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2013,
            'APP_TITLE' => 'Request from Abigail check nro 12000'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_TITLE' => 2014,
            'APP_TITLE' => 'Request from Abigail check nro 111'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        // Get first page, the major case title
        $results = Delegation::search(null, 0, 10, '1', null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(6, $results['data']);
        $this->assertEquals(2001, $results['data'][0]['APP_NUMBER']);
        $this->assertEquals('Request from Abigail check nro 25001', $results['data'][0]['APP_TITLE']);

        //Check the pagination
        $results = Delegation::search(null, 0, 5, '1', null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(5, $results['data']);
        $results = Delegation::search(null, 5, 2, '1', null, null, 'ASC',
            'APP_NUMBER', null, null, null, 'APP_TITLE');
        $this->assertCount(1, $results['data']);
    }

    /**
     * This ensures ordering ascending and descending works by case number APP_NUMBER
     * @test
     */
    public function it_should_sort_by_case_id()
    {
        factory(User::class, 100)->create();
        factory(Process::class, 1)->create();
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2001
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 30002
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
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
        factory(Process::class, 1)->create();
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 2001,
            'APP_TITLE' => 'Request by Thomas'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 30002,
            'APP_TITLE' => 'Request by Ariel'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
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
        factory(User::class, 100)->create();
        $process = factory(Process::class, 1)->create([
            'PRO_ID' => 2,
            'PRO_TITLE' => 'Egypt Supplier Payment Proposal'
        ]);
        factory(Delegation::class, 1)->create([
            'PRO_ID' => $process[0]->id
        ]);
        $process = factory(Process::class, 1)->create([
            'PRO_ID' => 1,
            'PRO_TITLE' => 'China Supplier Payment Proposal'
        ]);
        factory(Delegation::class, 1)->create([
            'PRO_ID' => $process[0]->id
        ]);
        $process = factory(Process::class, 1)->create([
            'PRO_ID' => 3,
            'PRO_TITLE' => 'Russia Supplier Payment Proposal'
        ]);
        factory(Delegation::class, 1)->create([
            'PRO_ID' => $process[0]->id
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
        factory(Process::class, 1)->create();

        $task = factory(Task::class, 1)->create([
            'TAS_ID' => 1000,
            'TAS_TITLE' => 'Initiate Request'
        ]);
        factory(Delegation::class, 1)->create([
            'TAS_ID' => $task[0]->TAS_ID
        ]);

        $task = factory(Task::class, 1)->create([
            'TAS_ID' => 4000,
            'TAS_TITLE' => 'Waiting for AP Manager Validation'
        ]);
        factory(Delegation::class, 1)->create([
            'TAS_ID' => $task[0]->TAS_ID
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
            'USR_ID' => $user->id
        ]);
        $user = factory(User::class)->create([
            'USR_USERNAME' => 'paul',
            'USR_LASTNAME' => 'Paul',
            'USR_FIRSTNAME' => 'Griffis',
        ]);
        // Create a new delegation, but for this specific user
        factory(Delegation::class)->create([
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->id
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
        factory(Process::class,1)->create();
        $application = factory(Application::class, 1)->create([
            'APP_UPDATE_DATE' => '2019-01-02 00:00:00'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_UPDATE_DATE' => '2019-01-03 00:00:00'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_UPDATE_DATE' => '2019-01-04 00:00:00'
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
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
        factory(Process::class,1)->create();
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
        factory(Process::class,1)->create();
        $application = factory(Application::class, 1)->create([
            'APP_STATUS' => 'DRAFT'
        ]);
        factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_STATUS' => 'TO_DO'
        ]);
        factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_STATUS' => 'COMPLETED'
        ]);
        factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_STATUS' => 'CANCELLED'
        ]);
        factory(Delegation::class, 25)->create([
            'APP_NUMBER' => $application[0]->APP_NUMBER
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
        factory(User::class, 100)->create();
        // Create a threads over the process
        $process = factory(Process::class, 1)->create([
            'PRO_ID' => 1
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 1,
            'APP_TITLE' => 'Request by Thomas',
        ]);
        // Create a user Gary in a thread
        $user = factory(User::class)->create([
            'USR_USERNAME' => 'gary',
            'USR_LASTNAME' => 'Bailey',
            'USR_FIRSTNAME' => 'Gary',
        ]);
        // Create a thread with the user Gary
        factory(Delegation::class, 1)->create([
            'PRO_ID' => $process[0]->id,
            'USR_ID' => $user->id,
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'CLOSED'
        ]);

        // Define a dummy task
        $task = factory(Task::class, 1)->create([
            'TAS_TYPE' => 'INTERMEDIATE-THROW'
        ]);
        // Create a thread with the dummy task this does not need a user
        factory(Delegation::class, 1)->create([
            'PRO_ID' => $process[0]->id,
            'USR_ID' => 0,
            'TAS_ID' => $task[0]->id,
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        // Create a user Paul in a thread
        $user = factory(User::class)->create([
            'USR_USERNAME' => 'paul',
            'USR_LASTNAME' => 'Griffis',
            'USR_FIRSTNAME' => 'Paul',
        ]);
        // Create a thread with the user Paul
        factory(Delegation::class, 1)->create([
            'PRO_ID' => $process[0]->id,
            'USR_ID' => $user->id,
            'APP_NUMBER' => $application[0]->APP_NUMBER,
            'DEL_THREAD_STATUS' => 'OPEN'
        ]);
        // Get first page, which is 25 of 26
        $results = Delegation::search(null, 0, 10, null, $process[0]->id, null, 'ASC', 'APP_NUMBER');
        $this->assertCount(1, $results['data']);
        $this->assertEquals('Griffis Paul', $results['data'][0]['APP_CURRENT_USER']);
    }

    /**
     * This ensures return the correct data by parallel task all threads CLOSED
     * @test
     */
    public function it_should_return_by_parallel_tasks_threads_closed()
    {
        factory(User::class,100)->create();
        factory(Process::class,1)->create();
        $task = factory(Task::class,1)->create([
            'TAS_TITLE' => 'Parallel task 1'
        ]);
        factory(Delegation::class, 5)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED'
        ]);
        $task = factory(Task::class,1)->create([
            'TAS_TITLE' => 'Parallel task 2'
        ]);
        factory(Delegation::class, 5)->create([
            'TAS_ID' => $task[0]->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED'
        ]);
        // Get first page, the order taskTitle
        $results = Delegation::search(null, 0, 2, null, null, null, 'ASC',
            'TAS_TITLE', null, null, null,'TAS_TITLE');
        $this->assertCount(0, $results['data']);

        // Get first page, the order taskTitle
        $results = Delegation::search(null, 0, 2, null, null, null, 'DESC',
            'TAS_TITLE', null, null, null,'TAS_TITLE');
        $this->assertCount(0, $results['data']);
    }

    /**
     * This ensures return the correct data by parallel task all threads OPEN
     * @test
     */
    public function it_should_return_by_parallel_tasks_threads_open()
    {
        factory(User::class,100)->create();
        factory(Process::class,1)->create();
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
}