<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\AppAssignSelfServiceValue;
use ProcessMaker\Model\AppAssignSelfServiceValueGroup;
use Tests\TestCase;

/**
 * Class AppAssignSelfServiceValueGroupTest
 *
 * @coversDefaultClass \ProcessMaker\Model\AppAssignSelfServiceValueGroup
 */
class AppAssignSelfServiceValueGroupTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test belongs to ID
     *
     * @covers \ProcessMaker\Model\AppAssignSelfServiceValueGroup::appSelfService()
     * @test
     */
    public function it_has_a_id_defined()
    {
        $table = AppAssignSelfServiceValueGroup::factory()->create([
            'ID' => function () {
                return AppAssignSelfServiceValue::factory()->create()->ID;
            }
        ]);
        $this->assertInstanceOf(AppAssignSelfServiceValue::class, $table->appSelfService);
    }
}