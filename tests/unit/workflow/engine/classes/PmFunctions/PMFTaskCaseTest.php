<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Task;
use Tests\TestCase;

/**
 * Test the PMFTaskCase() function
 *
 * @link https://wiki.processmaker.com/3.3/ProcessMaker_Functions/Case_Functions#PMFTaskCase.28.29
 */
class PMFTaskCaseTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFTaskCase"
     * @test
     */
    public function it_return_pending_tasks()
    {
        $task = Task::factory()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID
        ]);
        DB::commit();
        $result = PMFTaskCase($table->APP_UID);
        $this->assertNotEmpty($result);
    }
}
