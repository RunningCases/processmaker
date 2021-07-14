<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Participated;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * Class InboxTest
 *
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Participated
 */
class ParticipatedTest extends TestCase
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
     * Create participated cases factories
     *
     * @param string
     *
     * @return array
     */
    public function createParticipated()
    {
        $delegation = factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'CLOSED',
            'DEL_INDEX' => 1,
        ]);
        $delegation2 = factory(Delegation::class)->states('last_thread')->create([
            'APP_NUMBER' => $delegation->APP_NUMBER,
            'TAS_ID' => $delegation->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $delegation->USR_UID,
            'USR_ID' => $delegation->USR_ID,
            'PRO_ID' => $delegation->PRO_ID,
            'DEL_INDEX' => 2,
        ]);

        return $delegation2;
    }

    /**
     * Create many participated cases for one user
     * 
     * @param int
     * @return object
     */
    public function createMultipleParticipated($cases = 2)
    {
        $user = factory(\ProcessMaker\Model\User::class)->create();

        for ($i = 0; $i < $cases; $i = $i + 1) {
            $delegation = factory(Delegation::class)->states('foreign_keys')->create([
                'DEL_THREAD_STATUS' => 'CLOSED',
                'DEL_INDEX' => 1,
                'USR_UID' =>  $user->USR_UID,
                'USR_ID' =>  $user->USR_ID,
            ]);
            factory(Delegation::class)->states('last_thread')->create([
                'APP_UID' => $delegation->APP_UID,
                'APP_NUMBER' => $delegation->APP_NUMBER,
                'TAS_ID' => $delegation->TAS_ID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'USR_UID' => $delegation->USR_UID,
                'USR_ID' => $delegation->USR_ID,
                'PRO_ID' => $delegation->PRO_ID,
                'DEL_INDEX' => 2,
            ]);
        }
        return $user;
    }

    /**
     * It tests the getData without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setOrderByColumn()
     * @test
     */
    public function it_get_result_without_filters()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // Create new Participated object
        $participated = new Participated();
        // Set the user UID
        $participated->setUserUid($cases['USR_UID']);
        // Set the user ID
        $participated->setUserId($cases['USR_ID']);
        // Set OrderBYColumn value
        $participated->setOrderByColumn('APP_NUMBER');
        // Get the data
        $res = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEquals(2, count($res));
    }

    /**
     * It tests the getData the specific filter StartedByMe
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setCaseStatus()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setParticipatedStatus()
     * @test
     */
    public function it_filter_by_started_by_me()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // Create new Participated object
        $participated = new Participated();
        // Set the user UID
        $participated->setUserUid($cases->USR_UID);
        // Set the user ID
        $participated->setUserId($cases->USR_ID);
        // Get only the TO_DO
        $participated->setCaseStatus('TO_DO');
        // Set the filter STARTED
        $participated->setParticipatedStatus('STARTED');
        // Get the data
        $res = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEquals(1, count($res));
    }

    /**
     * It tests the getData the specific filter CompletedByMe
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setParticipatedStatus()
     * @test
     */
    public function it_filter_by_completed_by_me()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // Create new Participated object
        $participated = new Participated();
        // Set the user UID
        $participated->setUserUid($cases->USR_UID);
        // Set the user ID
        $participated->setUserId($cases->USR_ID);
        // Set the filter COMPLETED
        $participated->setParticipatedStatus('COMPLETED');
        // Get the data
        $res = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEquals(0, count($res));
    }

    /**
     * It tests the getData the specific filter setProcessId
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setFilterCases()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setProcessId()
     * @test
     */
    public function it_filter_by_process()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // Create new Participated object
        $participated = new Participated();
        // Set the filter
        $participated->setFilterCases('STARTED');
        // Set the user UID
        $participated->setUserUid($cases['USR_UID']);
        // Set the user ID
        $participated->setUserId($cases['USR_ID']);
        // Set the process
        $participated->setProcessId($cases['PRO_ID']);
        // Get the data
        $res = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEquals(2, count($res));
    }

    /**
     * It tests the getData method with case number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setCasesNumbers()
     * @test
     */
    public function it_filter_by_specific_cases()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // Create new Participated object
        $participated = new Participated();
        // Set the filter
        $participated->setFilterCases('STARTED');
        // Set the user UID
        $participated->setUserUid($cases['USR_UID']);
        // Set the user ID
        $participated->setUserId($cases['USR_ID']);
        // Set the case numbers
        $participated->setCasesNumbers([$cases['APP_NUMBER']]);
        // Get the data
        $res = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEquals(2, count($res));
    }

    /**
     * It tests the getData method with case number filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setRangeCasesFromTo()
     * @test
     */
    public function it_filter_by_range_cases()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // Create new Participated object
        $participated = new Participated();
        // Set the filter
        $participated->setFilterCases('STARTED');
        // Set the user UID
        $participated->setUserUid($cases['USR_UID']);
        // Set the user ID
        $participated->setUserId($cases['USR_ID']);
        // Set the range of case numbers
        $rangeOfCases = $cases['APP_NUMBER'] . "-" . $cases['APP_NUMBER'];
        $participated->setRangeCasesFromTo([$rangeOfCases]);
        // Get the data
        $res = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEquals(2, count($res));
    }

    /**
     * It tests the getData the specific filter setCaseTitle
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setFilterCases()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setCaseTitle()
     * @test
     */
    public function it_filter_by_thread_title()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // We need to commit the records inserted because is needed for the "fulltext" index
        DB::commit();
        // Create new Participated object
        $participated = new Participated();
        // Set the filter
        $participated->setFilterCases('STARTED');
        // Set the user UID
        $participated->setUserUid($cases->USR_UID);
        // Set the user ID
        $participated->setUserId($cases->USR_ID);
        // Set the title
        $participated->setCaseTitle($cases->DEL_TITLE);
        // Get the data
        $res = $participated->getData();
        // Asserts
        $this->assertCount(1, $res);
    }

    /**
     * It tests the getData the specific filter status
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setFilterCases()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setCaseStatus()
     * @test
     */
    public function it_filter_by_status()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // Create new Participated object
        $participated = new Participated();
        // Set the filter
        $participated->setFilterCases('STARTED');
        // Set the user UID
        $participated->setUserUid($cases['USR_UID']);
        // Set the user ID
        $participated->setUserId($cases['USR_ID']);
        // Set the case status
        $participated->setCaseStatus('TO_DO');
        // Get the data
        $result = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the getData the specific filter setStartCaseFrom and getStartCaseTo
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setFilterCases()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setStartCaseFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setStartCaseTo()
     * @test
     */
    public function it_filter_by_start_date()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // Create new Participated object
        $participated = new Participated();
        // Set the filter
        $participated->setFilterCases('STARTED');
        // Set the user UID
        $participated->setUserUid($cases['USR_UID']);
        // Set the user ID
        $participated->setUserId($cases['USR_ID']);
        // Set the range of dates
        $date = date('Y-m-d');
        $participated->setStartCaseFrom($date);
        $participated->setStartCaseTo($date);
        // Get the data
        $result = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEmpty($result);
    }

    /**
     * It tests the getData the specific filter setFinishCaseFrom and setFinishCaseTo
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setFilterCases()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserUid()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setFinishCaseFrom()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setFinishCaseTo()
     * @test
     */
    public function it_filter_by_finish_date()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // Create new Participated object
        $participated = new Participated();
        // Set the filter
        $participated->setFilterCases('STARTED');
        // Set the user UID
        $participated->setUserUid($cases['USR_UID']);
        // Set the user ID
        $participated->setUserId($cases['USR_ID']);
        // Set the range of dates
        $date = date('Y-m-d');
        $participated->setFinishCaseFrom($date);
        $participated->setFinishCaseTo($date);
        // Get the data
        $result = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEmpty($result);
    }

    /**
     * It tests the specific filter setParticipatedStatus = IN_PROGRESS
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setParticipatedStatus()
     * @test
     */
    public function it_get_status_in_progress()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // Create new Participated object
        $participated = new Participated();
        // Set the user UID
        $participated->setUserUid($cases->USR_UID);
        // Set the user ID
        $participated->setUserId($cases->USR_ID);
        // Set participated status
        $participated->setParticipatedStatus('IN_PROGRESS');
        // Get result
        $result = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertNotEmpty($result);
    }
    /**
     * It tests the specific filter setParticipatedStatus = COMPLETED
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setParticipatedStatus()
     * @test
     */
    public function it_get_status_completed()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // Create new Participated object
        $participated = new Participated();
        // Set the user UID
        $participated->setUserUid($cases->USR_UID);
        // Set the user ID
        $participated->setUserId($cases->USR_ID);
        // Set participated status
        $participated->setParticipatedStatus('COMPLETED');
        // Get result
        $result = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEmpty($result);
    }

    /**
     * It tests the getCounter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getCounter()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::setParticipatedStatus()
     * @test
     */
    public function it_get_counter()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // Create new Participated object
        $participated = new Participated();
        // Set the user UID
        $participated->setUserUid($cases->USR_UID);
        // Set the user ID
        $participated->setUserId($cases->USR_ID);
        // Set participated status
        $participated->setParticipatedStatus('IN_PROGRESS');
        // Get result
        $res = $participated->getCounter();
        // Assert the result of getCounter method
        $this->assertEquals(1, $res);
    }

    /**
     * It tests the getPagingCounters
     * 
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getPagingCounters()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
     * @test
     */
    public function it_should_test_get_paging_counters_method()
    {
        $cases = $this->createMultipleParticipated(3);
        $participated = new Participated();
        $participated->setUserId($cases->USR_ID);
        $participated->setUserUid($cases->USR_UID);
        $participated->setParticipatedStatus('STARTED');
        $res = $participated->getPagingCounters();
        $this->assertEquals(3, $res);

        $delegation = Delegation::select()->where('USR_ID', $cases->USR_ID)->first();
        $participated->setCaseNumber($delegation->APP_NUMBER);
        $participated->setProcessId($delegation->PRO_ID);
        $participated->setTaskId($delegation->TAS_ID);
        $participated->setCaseUid($delegation->APP_UID);
        $res = $participated->getPagingCounters();
        $this->assertEquals(1, $res);
    }
}