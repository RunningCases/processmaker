<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Search;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * Class SearchTest
 *
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Search
 */
class SearchTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Set up function.
     */
    public function setUp()
    {
        parent::setUp();
        Application::truncate();
        Delegation::truncate();
    }

    /**
     * Create participated cases factories
     *
     * @param int
     *
     * @return object
     */
    public function createSearch($rows = 10)
    {
        $delegation = factory(Delegation::class, $rows)->states('foreign_keys')->create();

        return $delegation;
    }

    /**
     * It tests the getData method without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @test
     */
    public function it_get_result_without_filters()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $result = $search->getData();
        // Asserts with the result
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getData with case number
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setCaseNumber()
     * @test
     */
    public function it_filter_by_app_number()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setCaseNumber($cases[0]->APP_NUMBER);
        $result = $search->getData();
        // Asserts with the result
        $this->assertEquals($cases[0]->APP_NUMBER, $result[0]['APP_NUMBER']);
    }

    /**
     * It tests the getData with specific case numbers
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setCasesNumbers()
     * @test
     */
    public function it_filter_by_specific_cases()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setCasesNumbers([$cases[0]->APP_NUMBER]);
        $result = $search->getData();
        // Asserts with the result
        $this->assertEquals($cases[0]->APP_NUMBER, $result[0]['APP_NUMBER']);
    }

    /**
     * It tests the getData with specific case numbers
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setRangeCasesFromTo()
     * @test
     */
    public function it_filter_by_range_cases()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $rangeOfCases = $cases[0]->APP_NUMBER . "-" . $cases[0]->APP_NUMBER;
        $search->setRangeCasesFromTo([$rangeOfCases]);
        $result = $search->getData();
        // Asserts with the result
        $this->assertNotEmpty($result);
    }

    /**
     * Tests the specific filter setCasesNumbers and setRangeCasesFromTo
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setCasesNumbers()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setRangeCasesFromTo()
     * @test
     */
    public function it_filter_by_cases_and_range_cases()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setCasesNumbers([$cases[0]->APP_NUMBER]);
        $rangeOfCases = $cases[0]->APP_NUMBER . "-" . $cases[0]->APP_NUMBER;
        $search->setRangeCasesFromTo([$rangeOfCases]);
        $result = $search->getData();
        // Asserts with the result
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getData with process
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setProcessId()
     * @test
     */
    public function it_filter_by_process()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setProcessId($cases[0]->PRO_ID);
        $result = $search->getData();
        // Asserts with the result
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getData with task
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setTaskId()
     * @test
     */
    public function it_filter_by_task()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setTaskId($cases[0]->TAS_ID);
        $result = $search->getData();
        // Asserts with the result
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getData method with case title filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setCaseTitle()
     * @test
     */
    public function it_filter_by_thread_title()
    {
        // Create factories related to the to_do cases
        $cases = $this->createSearch();
        $title = $cases->last()->DEL_TITLE;
        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();
        // Create new Draft object
        $search = new Search();
        // Set the title
        $search->setCaseTitle($title);
        // Get the data
        $res = $search->getData();
        // Asserts
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData with setCategoryId
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setCategoryId()
     * @test
     */
    public function it_filter_by_category()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setCategoryId(12);
        $result = $search->getData();
        // Asserts with the result
        $this->assertEmpty($result);
    }

    /**
     * It tests the getData with user
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setUserId()
     * @test
     */
    public function it_filter_by_user()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setUserId($cases[0]->USR_ID);
        $result = $search->getData();
        // Asserts with the result
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getData with user
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setUserStartedId()
     * @test
     */
    public function it_filter_by_user_started()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setUserStartedId($cases[0]->USR_ID);
        $result = $search->getData();
        // Asserts with the result
        $this->assertEmpty($result);
    }

    /**
     * It tests the getData with user completed
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setUserCompletedId()
     * @test
     */
    public function it_filter_by_user_completed()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setUserCompletedId($cases[0]->USR_ID);
        $result = $search->getData();
        // Asserts with the result
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getData with setStartCaseFrom and setStartCaseTo
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setStartCaseFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setStartCaseTo()
     * @test
     */
    public function it_filter_by_start_date()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $date = date('Y-m-d');
        $search->setStartCaseFrom($date);
        $search->setStartCaseTo($date);
        $result = $search->getData();
       // Asserts with the result
        $this->assertEmpty($result);
    }

    /**
     * It tests the getData with setFinishCaseFrom and setFinishCaseTo
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setFinishCaseFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setFinishCaseTo()
     * @test
     */
    public function it_filter_by_finish_date()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $date = date('Y-m-d');
        $search->setFinishCaseFrom($date);
        $search->setFinishCaseTo($date);
        $result = $search->getData();
        // Asserts with the result
        $this->assertEmpty($result);
    }

    /**
     * It tests the getData with status
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::setCaseStatuses()
     * @test
     */
    public function it_filter_by_status()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setCaseStatuses(['TO_DO']);
        $result = $search->getData();
        // Asserts with the result
        $this->assertNotEmpty($result);
    }

    /**
     * It tests web entry with negative appNumbers
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @test
     */
    public function it_get_web_entry_not_submitted()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        $casesNotSubmitted = factory(Delegation::class, 5)->states('web_entry')->create();
        // Create new Search object
        $search = new Search();
        $result = $search->getData();
        // Review if the cases not submitted are not considered
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getCounter method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getCounter()
     * @test
     */
    public function it_get_counter()
    {
        // Create new Search object
        $search = new Search();
        $total = $search->getCounter();
        // The count for search was disabled for performance issues
        $this->assertEquals($total, 0);
    }

    /**
     * It tests the getCounter method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::atLeastOne()
     * @test
     */
    public function it_get_at_least_one()
    {
        // Create new Search object
        $search = new Search();
        $res = $search->atLeastOne();
        // The count for search was disabled for performance issues
        $this->assertFalse($res);
    }

    /**
     * It tests the getPagingCounters method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getPagingCounters()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @test
     */
    public function it_should_test_the_counter_for_search()
    {
        // Create new Search object
        $search = new Search();
        $total = $search->getPagingCounters();
        // The count for search was disabled for performance issues
        $this->assertEquals($total, 0);
    }
}