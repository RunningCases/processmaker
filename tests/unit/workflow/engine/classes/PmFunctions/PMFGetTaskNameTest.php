<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Task;
use Tests\TestCase;

/**
 * Test the PMFGetTaskName() function
 *
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#PMFGetTaskName.28.29
 */
class PMFGetTaskNameTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFGetTaskName"
     * @test
     */
    public function it_return_task_name()
    {
        // Create task
        $task = Task::factory()->create();
        DB::commit();
        $result = PMFGetTaskName($task->TAS_UID);
        $this->assertNotEmpty($result);
        // When is empty
        $result = PMFGetTaskName('');
        $this->assertFalse($result);
    }
}
