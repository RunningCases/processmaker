<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessVariables;
use Tests\TestCase;

/**
 * @coversDefaultClass ProcessMaker\Model\ProcessVariables
 */
class ProcessVariablesTest extends TestCase
{
    /**
     * It tests the process scope in the ProcessVariables model
     * @test
     */
    public function it_should_test_process_scope_in_process_variables_model()
    {
        $process = factory(Process::class, 2)->create();

        factory(ProcessVariables::class)->create(
            [
                'PRJ_UID' => $process[0]['PRO_UID'],
                'VAR_SQL' => 'SELECT * FROM USERS WHERE USR_UID="213" UNION SELECT * from PROCESS'
            ]
        );

        factory(ProcessVariables::class)->create(
            [
                'PRJ_UID' => $process[1]['PRO_UID'],
                'VAR_SQL' => ''
            ]
        );

        factory(ProcessVariables::class)->create(
            [
                'PRJ_UID' => $process[0]['PRO_UID'],
                'VAR_SQL' => ''
            ]
        );

        $variablesQuery = ProcessVariables::query()->select();
        $variablesQuery->process($process[0]['PRO_UID']);
        $result = $variablesQuery->get()->values()->toArray();

        // Assert there are two process variables for the specific process
        $this->assertCount(2, $result);

        // Assert that the result has the correct filtered process
        $this->assertEquals($process[0]['PRO_UID'], $result[0]['PRJ_UID']);
        $this->assertEquals($process[0]['PRO_UID'], $result[1]['PRJ_UID']);
    }

    /**
     * Test it return the variables related to the PRO_ID
     *
     * @covers \ProcessMaker\Model\ProcessVariables::getVariables()
     * @test
     */
    public function it_list_variables_by_process()
    {
        $process = factory(Process::class)->create();

        factory(ProcessVariables::class)->create([
                'PRJ_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID,
            ]
        );
        $result = ProcessVariables::getVariables($process->PRO_ID);
        $this->assertNotEmpty($result);
    }
}