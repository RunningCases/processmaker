<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\ProcessUser;
use Tests\TestCase;

/**
 * Class ProcessUserTest
 *
 * @coversDefaultClass \ProcessMaker\Model\ProcessUser
 */
class ProcessUserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test get process of supervisor
     *
     * @covers \ProcessMaker\Model\ProcessUser::scopeProcessSupervisor()
     * @covers \ProcessMaker\Model\ProcessUser::scopeProcessGroupSupervisor()
     * @covers \ProcessMaker\Model\ProcessUser::scopeJoinProcess()
     * @covers \ProcessMaker\Model\ProcessUser::getProcessesOfSupervisor()
     * @test
     */
    public function it_get_process_of_supervisor()
    {
        $table = ProcessUser::factory()->foreign_keys()->create();
        $result = ProcessUser::getProcessesOfSupervisor($table->USR_UID);
        $this->assertNotEmpty($result);
    }
}