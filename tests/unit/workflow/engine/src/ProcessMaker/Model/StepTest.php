<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Step;
use Tests\TestCase;

/**
 * Class StepTest
 *
 * @coversDefaultClass \ProcessMaker\Model\Step
 */
class StepTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test get specific step
     *
     * @covers \ProcessMaker\Model\Step::getByProcessTaskAndStepType()
     * @test
     */
    public function it_get_specific_step()
    {
        $table = factory(Step::class)->create();
        $result = Step::getByProcessTaskAndStepType(
            $table->PRO_UID,
            $table->TAS_UID,
            $table->STEP_TYPE_OBJ,
            $table->STEP_UID_OBJ
        );
        $this->assertNotEmpty($result);
    }
}