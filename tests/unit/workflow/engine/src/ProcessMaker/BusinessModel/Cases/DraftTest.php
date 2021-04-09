<?php

namespace Tests\unit\workflow\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Draft;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\User;
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
        $usrId = User::getId($application['APP_INIT_USER']);
        $delegation = factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application->APP_INIT_USER,
            'USR_ID' => $usrId,
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
        ]);

        return $delegation;
    }

    /**
     * Create many draft cases for one user
     * 
     * @param int
     * @return object
     */
    public function createManyDraft($cases)
    {
        $user = factory(\ProcessMaker\Model\User::class)->create();

        for ($i = 0; $i < $cases; $i = $i + 1) {
            $application = factory(Application::class)->states('draft')->create([
                'APP_INIT_USER' => $user->USR_UID,
                'APP_CUR_USER' => $user->USR_UID,
            ]);
            factory(Delegation::class)->states('foreign_keys')->create([
                'DEL_THREAD_STATUS' => 'OPEN',
                'DEL_INDEX' => 1,
                'APP_UID' => $application->APP_UID,
                'APP_NUMBER' => $application->APP_NUMBER,
                'USR_ID' => $user->USR_ID
            ]);
        }

        return $user;
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
        $draft->setUserId($cases['USR_ID']);
        $draft->setUserUid($cases['USR_UID']);
        $result = $draft->getCounter();
        $this->assertTrue($result > 0);
    }

    /**
     * It tests the getData method without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @test
     */
    public function it_get_result_without_filters()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        // Set the user ID
        $draft->setUserId($cases['USR_ID']);
        $draft->setOrderByColumn('APP_NUMBER');
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with processId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_process()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases['USR_ID']);
        $draft->setProcessId($cases['PRO_ID']);
        $draft->setOrderByColumn('APP_NUMBER');
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with case number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_app_number()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases['USR_ID']);
        $draft->setCaseNumber($cases['APP_NUMBER']);
        $draft->setOrderByColumn('APP_NUMBER');
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with taskId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_task()
    {
        // Create factories related to the draft cases
        $cases = $this->createDraft();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($cases['USR_ID']);
        $draft->setTaskId($cases['TAS_ID']);
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method with case title filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_filter_by_thread_title()
    {
        // Create factories related to the to_do cases
        $cases = $this->createDraft();
        $usrId = $cases['USR_ID'];
        $title = $cases['DEL_TITLE'];
        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();
        // Create new Draft object
        $draft = new Draft();
        $draft->setUserId($usrId);
        // Set the title
        $draft->setCaseTitle($title);
        // Get the data
        $res = $draft->getData();
        // Asserts
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getData method using order by column
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getColumnsView()
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
        $draft->setUserId($cases['USR_ID']);
        // Define the column to order
        $draft->setOrderByColumn($columnsView[$index]);
        $res = $draft->getData();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the getPagingCounters() method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::getPagingCounters()
     * @covers \ProcessMaker\BusinessModel\Cases\Draft::filters()
     * @test
     */
    public function it_should_test_get_paging_counters_method()
    {
        $cases = $this->createManyDraft(3);

        $draft = new Draft();
        $draft->setUserId($cases->USR_ID);
        $draft->setUserUid($cases->USR_UID);

        $res = $draft->getPagingCounters();
        $this->assertEquals(3, $res);

        $delegation = Delegation::select()->where('USR_ID', $cases->USR_ID)->first();

        $draft->setCaseNumber($delegation->APP_NUMBER);
        $draft->setProcessId($delegation->PRO_ID);
        $draft->setTaskId($delegation->TAS_ID);
        $draft->setCaseUid($delegation->APP_UID);

        $res = $draft->getPagingCounters();

        $this->assertEquals(1, $res);
    }
}
