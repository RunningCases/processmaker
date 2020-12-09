<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Participated;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
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
     * It tests the getData method without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
     * @test
     */
    public function it_get_result_without_filters()
    {
        // Create factories related to the participated cases
        $cases = $this->createParticipated();
        // Create new Participated object
        $participated = new Participated();
        // Set the user UID
        $participated->setUserUid($cases->USR_UID);
        // Set the user ID
        $participated->setUserId($cases->USR_ID);
        // Set OrderBYColumn value
        $participated->setOrderByColumn('APP_NUMBER');
        // Call to getData method
        $res = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEquals(1, count($res));
    }

    /**
     * It tests the getData method with specific filter StartedByMe
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
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
        // Set the filter STARTED
        $participated->setParticipatedStatus('STARTED');
        // Set OrderBYColumn value
        $participated->setOrderByColumn('APP_NUMBER');
        // Call to getData method
        $res = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEquals(1, count($res));
    }

    /**
     * It tests the getData method with specific filter CompletedByMe
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
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
        // Set OrderBYColumn value
        $participated->setOrderByColumn('APP_NUMBER');
        // Call to getData method
        $res = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEquals(0, count($res));
    }

    /**
     * It tests the getData method with processId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
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
        $participated->setUserUid($cases->USR_UID);
        // Set the user ID
        $participated->setUserId($cases->USR_ID);
        // Set the process ID
        $participated->setProcessId($cases->PRO_ID);
        // Set OrderBYColumn value
        $participated->setOrderByColumn('APP_NUMBER');
        // Call to getData method
        $res = $participated->getData();
        // This assert that the expected numbers of results are returned
        $this->assertEquals(1, count($res));
    }

    /**
     * It tests the getData method with processId filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getData()
     * @covers \ProcessMaker\BusinessModel\Cases\Unassigned::getColumnsView()
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::filters()
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
     * It tests the getCounter method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Participated::getCounter()
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
}