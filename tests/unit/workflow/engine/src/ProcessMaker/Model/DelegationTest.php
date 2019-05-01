<?php
namespace Tests\unit\workflow\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\User;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Delegation;
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
        factory(\ProcessMaker\Model\User::class,100)->create();
        factory(\ProcessMaker\Model\Process::class,10)->create();
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
     * This checks to make sure pagination is working properly with search by caseTitle
     * @test
     */
    public function it_should_return_pages_of_data_filter_default_case_title()
    {
        factory(\ProcessMaker\Model\User::class,100)->create();
        factory(\ProcessMaker\Model\Process::class,10)->create();
        factory(\ProcessMaker\Model\Application::class,100)->create();
        factory(Delegation::class, 51)->create();
        $search = '#';
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, $search);
        $this->assertCount(10, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 25, 25, $search);
        $this->assertCount(10, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 50, 25, $search);
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure pagination is working properly with search by case title
     * @test
     */
    public function it_should_return_pages_of_data_filter_case_title()
    {
        factory(\ProcessMaker\Model\User::class,100)->create();
        factory(\ProcessMaker\Model\Process::class,10)->create();
        factory(\ProcessMaker\Model\Application::class,51)->create();
        factory(Delegation::class, 51)->create();
        $search = '#';
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 25, $search, null, null, null, null, null, null, null, 'APP_TITLE');
        $this->assertCount(10, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 0, 25, $search, null, null, null, null, null, null, null, 'APP_TITLE');
        $this->assertCount(10, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 0, 25, $search, null, null, null, null, null, null, null, 'APP_TITLE');
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure pagination is working properly with search by case number
     * @test
     */
    public function it_should_return_pages_of_data_filter_case_number()
    {
        factory(\ProcessMaker\Model\User::class,100)->create();
        factory(\ProcessMaker\Model\Process::class,10)->create();
        factory(\ProcessMaker\Model\Application::class,101)->create();
        factory(Delegation::class, 101)->create();
        $search = '1';
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 10, $search, null, null, null, null, null, null, null, 'APP_NUMBER');
        $this->assertCount(10, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 10, 10, $search, null, null, null, null, null, null, null, 'APP_NUMBER');
        $this->assertCount(10, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 20, 10, $search, null, null, null, null, null, null, null, 'APP_NUMBER');
        $this->assertCount(1, $results['data']);
    }

    /**
     * This checks to make sure pagination is working properly with search by case title
     * @test
     */
    public function it_should_return_pages_of_data_filter_task_title()
    {
        factory(\ProcessMaker\Model\User::class,100)->create();
        factory(\ProcessMaker\Model\Process::class,10)->create();
        factory(\ProcessMaker\Model\Task::class,200)->create();
        factory(Delegation::class, 51)->create();
        //I need to check the Faker names
        $search = 'task';
        // Get first page, which is 25
        $results = Delegation::search(null, 0, 10, $search, null, null, null, null, null, null, null, 'TAS_TITLE');
        $this->assertCount(10, $results['data']);
        // Get second page, which is 25 results
        $results = Delegation::search(null, 10, 10, $search, null, null, null, null, null, null, null, 'TAS_TITLE');
        $this->assertCount(10, $results['data']);
        // Get third page, which is only 1 result
        $results = Delegation::search(null, 20, 10, $search, null, null, null, null, null, null, null, 'TAS_TITLE');
        $this->assertCount(1, $results['data']);
    }

    /**
     * This ensures searching for a valid user works
     * @test
     */
    public function it_should_return_one_result_for_specified_user()
    {
        factory(\ProcessMaker\Model\User::class,100)->create();
        factory(\ProcessMaker\Model\Process::class,10)->create();
        // Create our unique user, with a unique username
        $user = factory(\ProcessMaker\Model\User::class)->create([
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
     * @test
     */
    public function it_should_sort_by_case_id()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function it_should_sort_by_user()
    {
        $this->markTestIncomplete();
    }
}