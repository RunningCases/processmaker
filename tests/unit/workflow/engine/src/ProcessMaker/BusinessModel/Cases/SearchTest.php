<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Search;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * Class InboxTest
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
        Delegation::truncate();
    }

    /**
     * Create participated cases factories
     *
     * @param int
     *
     * @return array
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
        // This assert that the expected numbers of results are returned
        $this->assertEquals(count($cases), count($result));
    }

    /**
     * It tests the getData with case number
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @test
     */
    public function it_filter_by_app_number()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setCaseNumber($cases[0]->APP_NUMBER);
        // Set order by column value
        $search->setOrderByColumn('APP_NUMBER');
        $result = $search->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEquals($cases[0]->APP_NUMBER, $result[0]['APP_NUMBER']);
    }

    /**
     * It tests the getData with process
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @test
     */
    public function it_filter_by_process()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setProcessId($cases[0]->PRO_ID);
        // Set order by column value
        $search->setOrderByColumn('APP_NUMBER');
        $result = $search->getData();
        // This assert that the expected numbers of results are returned
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getData with task
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @test
     */
    public function it_filter_by_task()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setTaskId($cases[0]->TAS_ID);
        // Set order by column value
        $search->setOrderByColumn('APP_NUMBER');
        $result = $search->getData();
        // This assert that the expected numbers of results are returned
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getData method with case title filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
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
     * It tests the getData with user
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @test
     */
    public function it_filter_by_user()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setUserId($cases[0]->USR_ID);
        // Set order by column value
        $search->setOrderByColumn('APP_NUMBER');
        $result = $search->getData();
        // This assert that the expected numbers of results are returned
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getData with priority
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Search::filters()
     * @test
     */
    public function it_filter_by_status()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setCaseStatuses(['TO_DO']);
        // Set order by column value
        $search->setOrderByColumn('APP_NUMBER');
        $result = $search->getData();
        // This assert that the expected numbers of results are returned
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getCounter method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getCounter()
     * @test
     */
    public function it_should_test_the_counter_for_search()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        // Set order by column value
        $search->setOrderByColumn('APP_NUMBER');
        $total = $search->getCounter();
        $this->assertEquals(count($cases), $total);
    }
}