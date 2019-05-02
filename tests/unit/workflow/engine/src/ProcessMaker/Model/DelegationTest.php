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
        factory(User::class,100)->create();
        factory(Process::class,10)->create();
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
     * This checks to make sure filter by process is working properly
     * @test
     */
    public function it_should_return_process_of_data()
    {
        factory(User::class,100)->create();
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
        $results = Delegation::search(null, 25, 25,null, $process[0]->id);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25,null, $process[0]->id);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure filter by status is working properly
     * Review status filter by a specific status, such as Draft
     * @test
     */
    public function it_should_return_status_draft_of_data()
    {
        factory(User::class,100)->create();
        factory(Process::class,1)->create();
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
        $results = Delegation::search(null, 25, 25,null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25,null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure filter by status is working properly
     * Review status filter by a specific status, such as To Do
     * @test
     */
    public function it_should_return_status_todo_of_data()
    {
        factory(User::class,100)->create();
        factory(Process::class,1)->create();
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
        $results = Delegation::search(null, 25, 25,null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25,null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure filter by status is working properly
     * Review status filter by a specific status, such as Completed
     * @test
     */
    public function it_should_return_status_completed_of_data()
    {
        factory(User::class,100)->create();
        factory(Process::class,1)->create();
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
        $results = Delegation::search(null, 25, 25,null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25,null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure filter by status is working properly
     * Review status filter by a specific status, such as Cancelled
     * @test
     */
    public function it_should_return_status_cancelled_of_data()
    {
        factory(User::class,100)->create();
        factory(Process::class,1)->create();
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
        $results = Delegation::search(null, 25, 25,null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(25, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25,null, null, $application[0]->APP_STATUS_ID);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This ensures searching for a valid user works
     * @test
     */
    public function it_should_return_one_result_for_specified_user()
    {
        factory(User::class,100)->create();
        factory(Process::class,10)->create();
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
     * @test
     */
    public function it_should_have_data_match_certain_schema()
    {
        $this->markTestIncomplete();
    }

    /**
     * This ensures ordering ascending works by case number
     * @test
     */
    public function it_should_sort_by_case_id()
    {
        factory(User::class,100)->create();
        factory(Process::class,1)->create();
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
     * This ensures ordering ascending works by case title
     * @test
     */
    public function it_should_sort_by_case_title()
    {
        factory(User::class,100)->create();
        factory(Process::class,1)->create();
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
     * This ensures ordering ascending and descending works by current user
     * @test
     */
    public function it_should_sort_by_user()
    {
        factory(User::class,100)->create();
        factory(Process::class,10)->create();
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
     * This checks to make sure filter by process is working properly
     * @test
     */
    public function it_should_return_data_issue()
    {
        factory(User::class,100)->create();
        // Create a threads over the process
        $process = factory(Process::class, 1)->create([
            'PRO_ID' => 1
        ]);
        $application = factory(Application::class, 1)->create([
            'APP_NUMBER' => 1,
            'APP_TITLE' => 'Request by Thomas',
            'APP_STATUS_ID' => 2,
            'APP_STATUS' => 'TO_DO'
        ]);
        // Create a user Gary in a thread
        $user = factory(User::class)->create([
            'USR_USERNAME' => 'gary',
            'USR_LASTNAME' => 'Gary',
            'USR_FIRSTNAME' => 'Bailey',
        ]);
        // Create a thread with the user Gary
        factory(Delegation::class, 1)->create([
            'PRO_ID' => $process[0]->id,
            'USR_ID' => $user->id,
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);

        // Define a dummy task
        $task = factory(Task::class, 1)->create([
            'TAS_ID' => 1,
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
            'USR_USERNAME' => 'Paul',
            'USR_LASTNAME' => 'Griffis',
            'USR_FIRSTNAME' => 'paul',
        ]);
        // Create a thread with the user Paul
        factory(Delegation::class, 1)->create([
            'PRO_ID' => $process[0]->id,
            'USR_ID' => $user->id,
            'APP_NUMBER' => $application[0]->APP_NUMBER
        ]);
        // Create others delegations
        factory(Delegation::class, 24)->create([
            'PRO_ID' => $process[0]->id
        ]);
        // Get first page, which is 25 of 26
        $results = Delegation::search(null, 0, 10, null, $process[0]->id, null, 'ASC', 'APP_NUMBER');
        $this->assertCount(10, $results['data']);
    }

    /**
     * This checks to make sure filter by category is working properly
     * @test
     */
    public function it_should_return_categories_of_data()
    {
        factory(User::class, 100)->create();
        // Dummy Processes
        factory(ProcessCategory::class, 4)->create();
        factory(Process::class, 4)->create([
            'PRO_CATEGORY' => \ProcessMaker\Model\ProcessCategory::all()->random()->CATEGORY_UID
        ]);
        // Dummy Delegations
        factory(Delegation::class, 100)->create([
            'PRO_ID' => \ProcessMaker\Model\Process::all()->random()->PRO_ID
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
}