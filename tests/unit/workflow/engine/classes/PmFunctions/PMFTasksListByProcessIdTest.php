<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Task;
use Tests\TestCase;

/**
 * Test the PMFTasksListByProcessId() function
 *
 * @link https://wiki.processmaker.com/3.2/ProcessMaker_Functions/Task_Functions#PMFTaskList.28.29
 */
class PMFTasksListByProcessIdTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFTasksListByProcessId"
     * @test
     */
    public function it_return_process_tasks()
    {
        // Create task
        $task = factory(Task::class)->create();
        DB::commit();
        $result = PMFTasksListByProcessId($task->PRO_UID);
        $this->assertNotEmpty($result);
    }
}
