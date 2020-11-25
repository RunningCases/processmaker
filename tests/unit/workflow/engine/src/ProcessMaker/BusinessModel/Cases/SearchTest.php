<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
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
     * @test
     */
    public function it_should_test_get_data_method_without_filters()
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
     * @test
     */
    public function it_should_test_get_data_method_with_specific_case_number()
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
     * It tests the getData with priority
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @test
     */
    public function it_should_test_get_data_method_with_specific_priority()
    {
        // Create factories related to the delegation cases
        $cases = $this->createSearch();
        // Create new Search object
        $search = new Search();
        $search->setPriority('N');
        // Set order by column value
        $search->setOrderByColumn('APP_NUMBER');
        $result = $search->getData();
        // This assert that the expected numbers of results are returned
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getData with process
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @test
     */
    public function it_should_test_get_data_method_with_specific_process()
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
     * @test
     */
    public function it_should_test_get_data_method_with_specific_task()
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
     * It tests the getData with user
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Search::getData()
     * @test
     */
    public function it_should_test_get_data_method_with_specific_user()
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