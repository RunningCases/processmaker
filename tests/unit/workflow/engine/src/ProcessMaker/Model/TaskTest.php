<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Delegation;
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
    public function it_should_return_title_of_event_task()
    {
        // Intermediate email event
        $task = factory(Task::class)->create([
            'TAS_TITLE' => 'INTERMEDIATE-THROW-EMAIL-EVENT'
        ]);
        $taskInstance = new Task();
        $title = $taskInstance->title($task->TAS_ID);
        $this->assertEquals($title, G::LoadTranslation('ID_INTERMEDIATE_THROW_EMAIL_EVENT'));
        // Intermediate throw message event
        $task = factory(Task::class)->create([
            'TAS_TITLE' => 'INTERMEDIATE-THROW-MESSAGE-EVENT'
        ]);
        $taskInstance = new Task();
        $title = $taskInstance->title($task->TAS_ID);
        $this->assertEquals($title, G::LoadTranslation('ID_INTERMEDIATE_THROW_MESSAGE_EVENT'));
        // Intermediate catch message event
        $task = factory(Task::class)->create([
            'TAS_TITLE' => 'INTERMEDIATE-CATCH-MESSAGE-EVENT'
        ]);
        $taskInstance = new Task();
        $title = $taskInstance->title($task->TAS_ID);
        $this->assertEquals($title, G::LoadTranslation('ID_INTERMEDIATE_CATCH_MESSAGE_EVENT'));
        // Intermediate timer event
        $task = factory(Task::class)->create([
            'TAS_TITLE' => 'INTERMEDIATE-CATCH-TIMER-EVENT'
        ]);
        $taskInstance = new Task();
        $title = $taskInstance->title($task->TAS_ID);
        $this->assertEquals($title, G::LoadTranslation('ID_INTERMEDIATE_CATCH_TIMER_EVENT'));
    }

    /**
     * This checks to load task properties
     *
     * @covers \ProcessMaker\Model\Task::load()
     * @test
     */
    public function it_should_return_task()
    {
        $task = factory(Task::class)->create();

        $taskInstance = new Task();
        $properties = $taskInstance->load($task->TAS_UID);
        $this->assertNotEmpty($properties);
    }

    /**
     * This checks to make get the task information from a user task
     *
     * @covers \ProcessMaker\Model\Task::information()
     * @test
     */
    public function it_should_return_task_information_from_user_task()
    {
        $task = factory(Task::class)->create([
            'TAS_TYPE' => 'NORMAL'
        ]);
        $del = factory(Delegation::class)->states('closed')->create([
            'PRO_UID' => $task->PRO_UID,
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_INIT_DATE' => '2020-07-26 16:42:08',
            'DEL_FINISH_DATE' => '2020-07-30 17:43:09',
        ]);
        $taskInstance = new Task();
        $taskInfo = $taskInstance->information($del->APP_UID, $del->TAS_UID, $del->DEL_INDEX);
        $result = ' 4 ' . G::LoadTranslation('ID_DAY_DAYS');
        $result .= ' 01 '. G::LoadTranslation('ID_HOUR_ABBREVIATE');
        $result .= ' 01 '. G::LoadTranslation('ID_MINUTE_ABBREVIATE');
        $result .= ' 01 '. G::LoadTranslation('ID_SECOND_ABBREVIATE');
        $this->assertEquals($taskInfo['DURATION'], $result);
    }

    /**
     * This checks to make get the task information from a automatic task
     *
     * @covers \ProcessMaker\Model\Task::information()
     * @test
     */
    public function it_should_return_task_information_from_automatic_task()
    {
        $task = factory(Task::class)->create([
            'TAS_TYPE' => 'SCRIPT-TASK'
        ]);
        $del = factory(Delegation::class)->states('closed')->create([
            'PRO_UID' => $task->PRO_UID,
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_DELEGATE_DATE' => '2020-07-26 16:42:08',
            'DEL_FINISH_DATE' => '2020-07-30 17:43:09',
        ]);
        $taskInstance = new Task();
        $taskInfo = $taskInstance->information($del->APP_UID, $del->TAS_UID, $del->DEL_INDEX);
        $result = ' 4 ' . G::LoadTranslation('ID_DAY_DAYS');
        $result .= ' 01 '. G::LoadTranslation('ID_HOUR_ABBREVIATE');
        $result .= ' 01 '. G::LoadTranslation('ID_MINUTE_ABBREVIATE');
        $result .= ' 01 '. G::LoadTranslation('ID_SECOND_ABBREVIATE');
        $this->assertEquals($taskInfo['DURATION'], $result);
    }
}