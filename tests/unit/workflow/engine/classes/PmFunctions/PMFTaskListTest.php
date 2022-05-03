<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Test the PMFTaskList() function
 *
 * @link https://wiki.processmaker.com/3.2/ProcessMaker_Functions/Task_Functions#PMFTaskList.28.29
 */
class PMFTaskListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFTaskList"
     * @test
     */
    public function it_return_pending_tasks()
    {
        // Create task
        $task = factory(Task::class)->create();
        // Create user
        $user = factory(User::class)->create();
        // Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        DB::commit();
        $result = PMFTaskList($user->USR_UID);
        $this->assertNotEmpty($result);
    }
}
