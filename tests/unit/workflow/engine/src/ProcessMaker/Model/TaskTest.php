<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Task;
use Tests\TestCase;

/**
 * Class TaskTest
 *
 * @coversDefaultClass \ProcessMaker\Model\Task
 */

class TaskTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This checks to make get the name of the task
     *
     * @covers \ProcessMaker\Model\Task::title()
     * @test
     */
    public function it_should_return_pages_of_data()
    {
        $task = factory(Task::class)->create();

        $taskInstance = new Task();
        $title = $taskInstance->title($task->TAS_ID);
        $this->assertEquals($title, $task->TAS_TITLE);
    }
}