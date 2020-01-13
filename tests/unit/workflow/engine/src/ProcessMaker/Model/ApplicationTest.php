<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Process;
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
     * This checks if return the columns used
     *
     * @covers \ProcessMaker\Model\Application::getByProUid()
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
     * @test
     */
    public function it_return_case_information()
    {
        $application = factory(Application::class)->create();
        $result = Application::getCase($application->APP_UID);
        $this->assertArrayHasKey('APP_STATUS', $result);
        $this->assertArrayHasKey('APP_INIT_USER', $result);
    }
}
