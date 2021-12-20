<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\BpmnProject;
use Tests\TestCase;

/**
 * Class BpmnProjectTest
 *
 * @coversDefaultClass \ProcessMaker\Model\BpmnProject
 */
class BpmnProjectTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test if is a BPMN process
     *
     * @covers \ProcessMaker\Model\BpmnProject::isBpmnProcess()
     * @test
     */
    public function it_is_bpmn_process()
    {
        $table = factory(BpmnProject::class)->create();
        $result = BpmnProject::isBpmnProcess($table->PRJ_UID);
        $this->assertEquals($result, 1);
    }
}