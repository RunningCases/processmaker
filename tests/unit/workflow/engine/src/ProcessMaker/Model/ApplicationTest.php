<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class ApplicationTest
 *
 * @coversDefaultClass \ProcessMaker\Model\Application
 */
class ApplicationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Set up function.
     */
    public function setUp()
    {
        parent::setUp();
        Application::truncate();
    }

    /**
     * Test belongs to APP_CUR_USER
     *
     * @covers \ProcessMaker\Model\Application::currentUser()
     * @test
     */
    public function it_has_a_current_user()
    {
        $application = factory(Application::class)->create([
            'APP_CUR_USER' => function () {
                return factory(User::class)->create()->USR_UID;
            }
        ]);
        $this->assertInstanceOf(User::class, $application->currentUser);
    }

    /**
     * Test belongs to APP_INIT_USER
     *
     * @covers \ProcessMaker\Model\Application::creatorUser()
     * @test
     */
    public function it_has_a_creator_user()
    {
        $application = factory(Application::class)->create([
            'APP_INIT_USER' => function () {
                return factory(User::class)->create()->USR_UID;
            }
        ]);
        $this->assertInstanceOf(User::class, $application->creatorUser);
    }

    /**
     * Test belongs to APP_INIT_USER
     *
     * @covers \ProcessMaker\Model\Application::creatorUser()
     * @test
     */
    public function it_has_a_init_user()
    {
        $application = factory(Application::class)->create([
            'APP_INIT_USER' => function () {
                return factory(User::class)->create()->USR_UID;
            }
        ]);
        $this->assertInstanceOf(User::class, $application->creatoruser);
    }

    /**
     * This test scopeUserId
     *
     * @covers \ProcessMaker\Model\Application::scopeUserId()
     * @covers \ProcessMaker\Model\Application::scopeJoinDelegation()
     * @test
     */
    public function it_return_scope_user_id()
    {
        $table = factory(Application::class)->states('foreign_keys')->create();
        $usrId = User::getId($table->APP_INIT_USER);
        $this->assertCount(1, $table->joinDelegation()->userId($usrId)->get());
    }

    /**
     * This test scopeCreator
     *
     * @covers \ProcessMaker\Model\Application::scopeCreator()
     * @test
     */
    public function it_return_scope_creator()
    {
        $table = factory(Application::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->creator($table->APP_INIT_USER)->get());
    }

    /**
     * This test scopeSpecificCasesByUid
     *
     * @covers \ProcessMaker\Model\Application::scopeSpecificCasesByUid()
     * @test
     */
    public function it_return_scope_case_uids()
    {
        $table = factory(Application::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->specificCasesByUid([$table->APP_UID])->get());
    }

    /**
     * This test scopeCase
     *
     * @covers \ProcessMaker\Model\Application::scopeCase()
     * @test
     */
    public function it_return_scope_case()
    {
        $table = factory(Application::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->case($table->APP_NUMBER)->get());
    }

    /**
     * This test scopePositiveCases
     *
     * @covers \ProcessMaker\Model\Application::scopePositiveCases()
     * @test
     */
    public function it_return_scope_positive_cases()
    {
        $table = factory(Application::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->positiveCases()->get());
    }

    /**
     * This test scopeSpecificCases
     *
     * @covers \ProcessMaker\Model\Application::scopeSpecificCases()
     * @test
     */
    public function it_return_scope_specific_case_numbers()
    {
        $table = factory(Application::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->specificCases([$table->APP_NUMBER])->get());
    }

    /**
     * This test scopeRangeOfCases
     *
     * @covers \ProcessMaker\Model\Application::scopeRangeOfCases()
     * @test
     */
    public function it_return_scope_range_of_cases()
    {
        $table = factory(Application::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->rangeOfCases([$table->APP_NUMBER.'-'.$table->APP_NUMBER])->get());
    }

    /**
     * This test scopeCasesFrom
     *
     * @covers \ProcessMaker\Model\Application::scopeCasesFrom()
     * @test
     */
    public function it_return_scope_case_from()
    {
        $table = factory(Application::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->casesFrom($table->APP_NUMBER)->get());
    }

    /**
     * This test scopeCasesTo
     *
     * @covers \ProcessMaker\Model\Application::scopeCasesTo()
     * @test
     */
    public function it_return_scope_case_to()
    {
        $table = factory(Application::class)->states('foreign_keys')->create();
        $this->assertCount(1, $table->casesTo($table->APP_NUMBER)->get());
    }

    /**
     * This checks if return the columns used
     *
     * @covers \ProcessMaker\Model\Application::scopeStatusId()
     * @test
     */
    public function it_return_cases_by_status_id()
    {
        $table = factory(Application::class)->create();
        $this->assertCount(1, $table->statusId($table->APP_STATUS_ID)->get());
    }

    /**
     * This test scopeStatusIds
     *
     * @covers \ProcessMaker\Model\Application::scopeStatusIds()
     * @test
     */
    public function it_return_cases_by_status_ids()
    {
        $table = factory(Application::class)->create();
        $this->assertCount(1, $table->statusIds([$table->APP_STATUS_ID])->get());
    }

    /**
     * This test scopeStartDateFrom
     *
     * @covers \ProcessMaker\Model\Application::scopeStartDateFrom()
     * @test
     */
    public function it_return_start_date_from()
    {
        $table = factory(Application::class)->create();
        $this->assertCount(1, $table->startDateFrom($table->APP_CREATE_DATE->format("Y-m-d H:i:s"))->get());
    }

    /**
     * This test scopeStartDateTo
     *
     * @covers \ProcessMaker\Model\Application::scopeStartDateTo()
     * @test
     */
    public function it_return_start_date_to()
    {
        $table = factory(Application::class)->create();
        $this->assertCount(1, $table->startDateTo($table->APP_CREATE_DATE->format("Y-m-d H:i:s"))->get());
    }

    /**
     * This test scopeFinishCaseFrom
     *
     * @covers \ProcessMaker\Model\Application::scopeFinishCaseFrom()
     * @test
     */
    public function it_return_finish_date_from()
    {
        $table = factory(Application::class)->create();
        $this->assertCount(1, $table->finishCaseFrom($table->APP_FINISH_DATE->format("Y-m-d H:i:s"))->get());
    }

    /**
     * This test scopeFinishCaseTo
     *
     * @covers \ProcessMaker\Model\Application::scopeFinishCaseTo()
     * @test
     */
    public function it_return_finish_date_to()
    {
        $table = factory(Application::class)->create();
        $this->assertCount(1, $table->finishCaseTo($table->APP_FINISH_DATE->format("Y-m-d H:i:s"))->get());
    }

    /**
     * This test scopeTask
     *
     * @covers \ProcessMaker\Model\Application::scopeTask()
     * @covers \ProcessMaker\Model\Application::scopeJoinDelegation()
     * @test
     */
    public function it_return_scope_task()
    {
        $table = factory(Application::class)->create();
        $tableJoin = factory(Delegation::class)->states('foreign_keys')->create([
            'APP_UID' => $table->APP_UID,
            'APP_NUMBER' => $table->APP_NUMBER,
        ]);

        $this->assertCount(1, $table->joinDelegation()->task($tableJoin->TAS_ID)->get());
    }

    /**
     * This test scopeJoinProcess
     *
     * @covers \ProcessMaker\Model\Application::scopeJoinProcess()
     * @test
     */
    public function it_return_scope_join_process()
    {
        $table = factory(Application::class)->create();
        $this->assertCount(1, $table->joinProcess()->get());
    }

    /**
     * This checks if return the columns used
     *
     * @covers \ProcessMaker\Model\Application::getByProUid()
     * @covers \ProcessMaker\Model\Application::scopeProUid()
     * @test
     */
    public function it_return_cases_by_process()
    {
        $process = factory(Process::class)->create();
        factory(Application::class, 5)->create(['PRO_UID' => $process->PRO_UID]);
        $cases = Application::getByProUid($process->PRO_UID);
        foreach ($cases as $case) {
            $this->assertEquals($case->PRO_UID, $process->PRO_UID);
        }
    }

    /**
     * This checks if return the columns used
     *
     * @covers \ProcessMaker\Model\Application::getCase()
     * @covers \ProcessMaker\Model\Application::scopeAppUid()
     * @test
     */
    public function it_return_case_information()
    {
        $application = factory(Application::class)->create();
        $result = Application::getCase($application->APP_UID);
        $this->assertArrayHasKey('APP_STATUS', $result);
        $this->assertArrayHasKey('APP_INIT_USER', $result);
    }

    /**
     * This review if get the case number
     *
     * @covers \ProcessMaker\Model\Application::getCaseNumber()
     * @test
     */
    public function it_get_case_number()
    {
        $application = factory(Application::class)->create();
        $result = Application::getCaseNumber($application->APP_UID);
        // When the application exist
        $this->assertEquals($result, $application->APP_NUMBER);
        // When the application does not exist
        $appFake = G::generateUniqueID();
        $result = Application::getCaseNumber($appFake);
        $this->assertEquals($result, 0);
    }

    /**
     * This checks if the columns was updated correctly
     *
     * @covers \ProcessMaker\Model\Application::updateColumns()
     * @test
     */
    public function it_update_columns()
    {
        // No column will be updated
        $application = factory(Application::class)->create();
        $result = Application::updateColumns($application->APP_UID, []);
        $this->isEmpty($result);

        // Tried to update APP_ROUTING_DATA
        $application = factory(Application::class)->create();
        $result = Application::updateColumns($application->APP_UID, ['APP_ROUTING_DATA' => '']);
        $this->assertArrayHasKey('APP_ROUTING_DATA', $result);

        // We can not update with a empty user
        $application = factory(Application::class)->create();
        $result = Application::updateColumns($application->APP_UID, ['APP_CUR_USER' => '']);
        $this->assertArrayNotHasKey('APP_CUR_USER', $result);

        // Tried to update APP_CUR_USER
        $application = factory(Application::class)->create();
        $result = Application::updateColumns($application->APP_UID, ['APP_CUR_USER' => '00000000000000000000000000000001']);
        $this->assertArrayHasKey('APP_CUR_USER', $result);
    }

    /**
     * Count cases per process
     *
     * @covers \ProcessMaker\Model\Application::getCountByProUid()
     * @covers \ProcessMaker\Model\Application::scopeProUid()
     * @covers \ProcessMaker\Model\Application::scopeStatusId()
     * @covers \ProcessMaker\Model\Application::scopePositiveCases()
     * @test
     */
    public function it_count_cases_by_process()
    {
        $process = factory(Process::class)->create();
        factory(Application::class, 5)->create(['PRO_UID' => $process->PRO_UID]);
        $result = Application::getCountByProUid($process->PRO_UID);
        $this->assertEquals($result, 5);
    }
}
