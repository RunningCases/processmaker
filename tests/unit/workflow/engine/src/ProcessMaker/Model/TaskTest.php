<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use G;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\ElementTaskRelation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use Tests\TestCase;

/**
 * Class TaskTest
 *
 * @coversDefaultClass \ProcessMaker\Model\Task
 */

class TaskTest extends TestCase
{
    /**
     * Set up method.
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->truncateNonInitialModels();
    }

    /**
     * It tests the get taskId
     *
     * @covers \ProcessMaker\Model\Task::getTask()
     * @test
     */
    public function it_get_task()
    {
        $task = Task::factory()->create();
        $result = Task::getTask($task->TAS_ID);
        $this->assertNotEmpty($result);
    }

    /**
     * This test scopeExcludedTasks
     *
     * @covers \ProcessMaker\Model\Task::scopeExcludedTasks()
     * @test
     */
    public function it_scope_exclude_tasks()
    {
        $table = Task::factory()->create();
        $this->assertNotEmpty($table->excludedTasks()->get());
    }

    /**
     * This checks to make get the name of the task
     *
     * @covers \ProcessMaker\Model\Task::title()
     * @test
     */
    public function it_should_return_title_of_event_task()
    {
        // Intermediate email event
        $task = Task::factory()->create([
            'TAS_TITLE' => 'INTERMEDIATE-THROW-EMAIL-EVENT',
            'TAS_TYPE' => 'INTERMEDIATE-THROW-EMAIL-EVENT'
        ]);
        $taskInstance = new Task();
        $title = $taskInstance->title($task->TAS_ID);
        $this->assertEquals($title['title'], G::LoadTranslation('ID_INTERMEDIATE_THROW_EMAIL_EVENT'));
        // Intermediate throw message event
        $task = Task::factory()->create([
            'TAS_TITLE' => 'INTERMEDIATE-THROW-MESSAGE-EVENT',
            'TAS_TYPE' => 'INTERMEDIATE-THROW-MESSAGE-EVENT'
        ]);
        $taskInstance = new Task();
        $title = $taskInstance->title($task->TAS_ID);
        $this->assertEquals($title['title'], G::LoadTranslation('ID_INTERMEDIATE_THROW_MESSAGE_EVENT'));
        // Intermediate catch message event
        $task = Task::factory()->create([
            'TAS_TITLE' => 'INTERMEDIATE-CATCH-MESSAGE-EVENT',
            'TAS_TYPE' => 'INTERMEDIATE-CATCH-MESSAGE-EVENT'
        ]);
        $taskInstance = new Task();
        $title = $taskInstance->title($task->TAS_ID);
        $this->assertEquals($title['title'], G::LoadTranslation('ID_INTERMEDIATE_CATCH_MESSAGE_EVENT'));
        // Intermediate timer event
        $task = Task::factory()->create([
            'TAS_TITLE' => 'INTERMEDIATE-CATCH-TIMER-EVENT',
            'TAS_TYPE' => 'INTERMEDIATE-CATCH-TIMER-EVENT'
        ]);
        $taskInstance = new Task();
        $title = $taskInstance->title($task->TAS_ID);
        $this->assertEquals($title['title'], G::LoadTranslation('ID_INTERMEDIATE_CATCH_TIMER_EVENT'));
        // Script task
        $task = Task::factory()->create([
            'TAS_TITLE' => 'SCRIPT-TASK',
            'TAS_TYPE' => 'SCRIPT-TASK'
        ]);
        $taskInstance = new Task();
        $title = $taskInstance->title($task->TAS_ID);
        $this->assertEquals($title['title'], G::LoadTranslation('ID_SCRIPT_TASK_UNTITLED'));
        // Service task
        $task = Task::factory()->create([
            'TAS_TITLE' => 'SERVICE-TASK',
            'TAS_TYPE' => 'SERVICE-TASK'
        ]);
        $taskInstance = new Task();
        $title = $taskInstance->title($task->TAS_ID);
        $this->assertEquals($title['title'], G::LoadTranslation('ID_SERVICE_TASK_UNTITLED'));
        // None
        $task = Task::factory()->create([
            'TAS_TITLE' => 'SUBPROCESS',
            'TAS_TYPE' => 'SUBPROCESS'
        ]);
        $taskInstance = new Task();
        $title = $taskInstance->title($task->TAS_ID);
        $this->assertEquals($title['title'], G::LoadTranslation('ID_ANONYMOUS'));
    }

    /**
     * This checks to load task properties
     *
     * @covers \ProcessMaker\Model\Task::load()
     * @test
     */
    public function it_should_return_task()
    {
        $task = Task::factory()->create();

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
        $task = Task::factory()->create([
            'TAS_TYPE' => 'NORMAL'
        ]);
        $del = Delegation::factory()->closed()->create([
            'PRO_UID' => $task->PRO_UID,
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_INIT_DATE' => '2020-07-26 16:42:08',
            'DEL_FINISH_DATE' => '2020-07-30 17:43:09',
        ]);
        $taskInstance = new Task();
        $taskInfo = $taskInstance->information($del->APP_UID, $del->TAS_UID, $del->DEL_INDEX);
        $result = ' 4 ' . G::LoadTranslation('ID_DAY_DAYS');
        $result .= ' 01 ' . G::LoadTranslation('ID_HOUR_ABBREVIATE');
        $result .= ' 01 ' . G::LoadTranslation('ID_MINUTE_ABBREVIATE');
        $result .= ' 01 ' . G::LoadTranslation('ID_SECOND_ABBREVIATE');
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
        $task = Task::factory()->create([
            'TAS_TYPE' => 'SCRIPT-TASK'
        ]);
        $del = Delegation::factory()->closed()->create([
            'PRO_UID' => $task->PRO_UID,
            'TAS_ID' => $task->TAS_ID,
            'TAS_UID' => $task->TAS_UID,
            'DEL_DELEGATE_DATE' => '2020-07-26 16:42:08',
            'DEL_FINISH_DATE' => '2020-07-30 17:43:09',
        ]);
        $taskInstance = new Task();
        $taskInfo = $taskInstance->information($del->APP_UID, $del->TAS_UID, $del->DEL_INDEX);
        $result = ' 4 ' . G::LoadTranslation('ID_DAY_DAYS');
        $result .= ' 01 ' . G::LoadTranslation('ID_HOUR_ABBREVIATE');
        $result .= ' 01 ' . G::LoadTranslation('ID_MINUTE_ABBREVIATE');
        $result .= ' 01 ' . G::LoadTranslation('ID_SECOND_ABBREVIATE');
        $this->assertEquals($taskInfo['DURATION'], $result);
    }

    /**
     * It tests the setTaskDefTitle() method
     * 
     * @covers \ProcessMaker\Model\Task::setTaskDefTitle()
     * @test
     */
    public function it_should_test_set_task_title_method()
    {
        $project = Process::factory()->create();
        $task = Task::factory()->create([
            'TAS_DEF_TITLE' => 'something'
        ]);
        $elementTask = ElementTaskRelation::factory()->create([
            'PRJ_UID' => $project->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
        ]);

        Task::setTaskDefTitle($elementTask->ELEMENT_UID, "Task title new");
        $query = Task::query();
        $query->select()->where('TASK.TAS_UID', $task->TAS_UID);
        $res = $query->get()->values()->toArray();
        $this->assertEquals($res[0]['TAS_DEF_TITLE'], 'Task title new');
    }

    /**
     * It tests the getTaskDefTitle() method
     * 
     * @covers \ProcessMaker\Model\Task::getTaskDefTitle()
     * @test
     */
    public function it_should_test_get_task_def_title_method()
    {
        $project = Process::factory()->create();
        $task = Task::factory()->create([
            'TAS_DEF_TITLE' => 'something'
        ]);
        $elementTask = ElementTaskRelation::factory()->create([
            'PRJ_UID' => $project->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
        ]);

        $res = Task::getTaskDefTitle($elementTask->ELEMENT_UID);

        $this->assertEquals($res, $task->TAS_DEF_TITLE);
    }

    /**
     * It tests the get case title defined in the task
     *
     * @covers \ProcessMaker\Model\Task::taskCaseTitle()
     * @test
     */
    public function it_get_case_title()
    {
        $task = Task::factory()->create();
        $tas = new Task();
        $result = $tas->taskCaseTitle($task->TAS_UID);
        $this->assertNotEmpty($result);
    }

    /**
     * It test get tasks for the new home view
     *
     * @covers \ProcessMaker\Model\Task::getTasksForHome()
     * @covers \ProcessMaker\Model\Task::scopeTitle()
     * @covers \ProcessMaker\Model\Task::scopeProcess()
     * @test
     */
    public function it_should_test_get_tasks_for_home_method()
    {
        $process1 = Process::factory()->create();
        $process2 = Process::factory()->create();

        Task::factory()->create([
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TITLE' => 'Task 1'
        ]);
        Task::factory()->create([
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TITLE' => 'Task 2'
        ]);
        Task::factory()->create([
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TITLE' => 'Task 3'
        ]);

        Task::factory()->create([
            'PRO_UID' => $process2->PRO_UID,
            'TAS_TITLE' => 'Task 1'
        ]);
        Task::factory()->create([
            'PRO_UID' => $process2->PRO_UID,
            'TAS_TITLE' => 'Task 2'
        ]);

        $this->assertCount(5, Task::getTasksForHome());
        $this->assertCount(2, Task::getTasksForHome('Task 1'));
        $this->assertCount(3, Task::getTasksForHome(null, $process1->PRO_ID));
        $this->assertCount(5, Task::getTasksForHome(null, null, null, 2));
        $this->assertCount(1, Task::getTasksForHome(null, null, 2, 1));
    }
}
