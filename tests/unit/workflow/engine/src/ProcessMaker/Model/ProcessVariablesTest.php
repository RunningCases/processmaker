<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessVariables;
use Tests\TestCase;

/**
 * Class ProcessVariablesTest
 *
 * @coversDefaultClass \ProcessMaker\Model\ProcessVariables
 */
class ProcessVariablesTest extends TestCase
{
    /**
     * It tests the process scope in the ProcessVariables model
     * @test
     */
    public function it_should_test_process_scope_in_process_variables_model()
    {
        $process = Process::factory(2)->create();

        ProcessVariables::factory()->create(
            [
                'PRJ_UID' => $process[0]['PRO_UID'],
                'VAR_SQL' => 'SELECT * FROM USERS WHERE USR_UID="213" UNION SELECT * from PROCESS'
            ]
        );

        ProcessVariables::factory()->create(
            [
                'PRJ_UID' => $process[1]['PRO_UID'],
                'VAR_SQL' => ''
            ]
        );

        ProcessVariables::factory()->create(
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
     * Test it return a variable related to the VAR_UID
     *
     * @covers \ProcessMaker\Model\ProcessVariables::getVariable()
     * @test
     */
    public function it_get_variable()
    {
        $table = ProcessVariables::factory()->create();
        $result = ProcessVariables::getVariable($table->VAR_UID);
        $this->assertNotEmpty($result);
    }

    /**
     * Test it return the variables related to the PRO_ID
     *
     * @covers \ProcessMaker\Model\ProcessVariables::getVariables()
     * @test
     */
    public function it_list_variables_by_process()
    {
        $process = Process::factory()->create();

        ProcessVariables::factory()->create([
                'PRJ_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID,
            ]
        );
        $result = ProcessVariables::getVariables($process->PRO_ID);
        $this->assertNotEmpty($result);
    }

    /**
     * Test it return the variables by type related to the PRO_ID
     *
     * @covers \ProcessMaker\Model\ProcessVariables::scopeProcessId()
     * @covers \ProcessMaker\Model\ProcessVariables::scopeTypeId()
     * @covers \ProcessMaker\Model\ProcessVariables::getVariablesByType()
     * @test
     */
    public function it_list_variables_type_by_process()
    {
        $process = Process::factory()->create();
        $varType = 'integer';
        $varTypeId = 2;
        for ($x = 1; $x <= 5; $x++) {
            $processVar = ProcessVariables::factory()->foreign_keys()->create([
                'PRO_ID' => $process->PRO_ID,
                'PRJ_UID' => $process->PRO_UID,
                'VAR_FIELD_TYPE' => $varType,
                'VAR_FIELD_TYPE_ID' => $varTypeId,
                'VAR_NAME' => 'varTestName' . $x,
            ]);
        }

        $res = ProcessVariables::getVariablesByType($processVar->PRO_ID, 2, null, null, null);
        $this->assertNotEmpty($res);
        $this->assertEquals(5, count($res));
        // Get a specific start and limit
        $res = ProcessVariables::getVariablesByType($process->PRO_ID, 2, 0, 2);
        $this->assertNotEmpty($res);
        $this->assertEquals(2, count($res));
        // Get a specific search
        $res = ProcessVariables::getVariablesByType($process->PRO_ID, 2, 0, 4, 'varTest');
        $this->assertNotEmpty($res);
        $this->assertEquals(4, count($res));
        // When the search does not match
        $res = ProcessVariables::getVariablesByType($process->PRO_ID, 2, null, null, 'other');
        $this->assertEmpty($res);
    }
}