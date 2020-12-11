<?php

namespace Tests\unit\workflow\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Draft;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Draft
 */
class DraftTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Method set up.
     */
    public function setUp()
    {
        parent::setUp();
        $this->markTestIncomplete();
    }

    /**
     * Create draft cases factories
     *
     * @param string
     *
     * @return array
     */
    public function createDraft()
    {
        $application = factory(Application::class)->states('draft')->create();
        $delegation = factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
        ]);

        return $delegation;
    }

    /**
     * This checks the counters is working properly in draft
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getCounter()
     * @test
     */
    public function it_should_count_cases()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases->USR_ID);
        $result = $draft->getCounter();
        $this->assertTrue($result > 0);
    }

    /**
     * It tests the getData method without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
     * @test
     */
    public function it_get_result_without_filters()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Inbox();
        // Set the user ID
        $draft->setUserId($cases->USR_ID);
        $draft->setOrderByColumn('APP_NUMBER');
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with processId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_process()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases->USR_ID);
        $draft->setProcessId($cases->PRO_ID);
        $draft->setOrderByColumn('APP_NUMBER');
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with case number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_app_number()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases->USR_ID);
        $draft->setCaseNumber($cases->APP_NUMBER);
        $draft->setOrderByColumn('APP_NUMBER');
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with taskId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_task()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases->USR_ID);
        $draft->setTaskId($cases->TAS_ID);
        $res = $draft->getData();
        $this->assertNotEmpty($res);

    }

    /**
     * It tests the getData method with case title filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_thread_title()
    {
        // Create factories related to the to_do cases
        $cases = $this->createDraft();
        $title = $cases->last()->DEL_TITLE;
        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases->USR_ID);
        // Set the title
        $draft->setCaseTitle($cases->DEL_TITLE);
        // Get the data
        $res = $draft->getData();
        // Asserts
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method using order by column
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_order_by_column()
    {
        // Create factories related to the to_do cases
        $cases = $this->createDraft();
        $columnsView = [
            'APP_NUMBER',
            'DEL_TITLE',
            'PRO_TITLE',
            'TAS_TITLE',
            'DEL_TASK_DUE_DATE',
            'DEL_DELEGATE_DATE'
        ];
        $index = array_rand($columnsView);
        // Create new Inbox object
        $draft = new Draft();
        $draft->setUserId($cases->USR_ID);
        // Define the column to order
        $draft->setOrderByColumn($columnsView[$index]);
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }
}