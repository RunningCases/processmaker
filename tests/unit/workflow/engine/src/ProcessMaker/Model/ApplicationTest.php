<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class DelegationTest
 *
 * @coversDefaultClass \ProcessMaker\Model\Application
 */
class ApplicationTest extends TestCase
{
    use DatabaseTransactions;

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
     * @covers \ProcessMaker\Model\Application::scopePositivesCases()
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
