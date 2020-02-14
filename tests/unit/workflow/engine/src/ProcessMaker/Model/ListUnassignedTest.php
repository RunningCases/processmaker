<?php
namespace Tests\unit\workflow\src\ProcessMaker\Model;

use ProcessMaker\Model\ListUnassigned;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessCategory;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class ListUnassignedTest
 *
 * @coversDefaultClass \ProcessMaker\Model\ListUnassigned
 */
class ListUnassignedTest extends TestCase
{
    /**
     * This checks to make sure pagination is working properly
     *
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_return_pages_of_data()
    {
        $user = factory(User::class)->create();
        for ($x = 1; $x <= 5; $x++) {
            $list = factory(ListUnassigned::class)->states('foreign_keys')->create();
            factory(TaskUser::class)->create([
                'TAS_UID' => $list->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
        }

        // Define the filters
        $filters = ['start' => 0, 'limit' => 2];
        // Get data first page
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertCount(2, $result);
        // Get data second page
        $filters = ['start' => 2, 'limit' => 2];
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertCount(2, $result);
        // Get data third page
        $filters = ['start' => 4, 'limit' => 2];
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertCount(1, $result);
    }

    /**
     * This ensures ordering ascending and descending works by case number APP_NUMBER
     *
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_sort_by_case_number()
    {
        $user = factory(User::class)->create();
        for ($x = 1; $x <= 5; $x++) {
            $list = factory(ListUnassigned::class)->states('foreign_keys')->create();
            factory(TaskUser::class)->create([
                'TAS_UID' => $list->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
        }
        //Define the filters
        $filters = ['sort' => 'APP_NUMBER', 'dir' => 'ASC'];
        //Get data
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertGreaterThan($result[0]['APP_NUMBER'], $result[1]['APP_NUMBER']);

        //Define the filters
        $filters = ['sort' => 'APP_NUMBER', 'dir' => 'DESC'];
        //Get data
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertGreaterThan($result[1]['APP_NUMBER'], $result[0]['APP_NUMBER']);
    }

    /**
     * This ensures ordering ascending and descending works by case number APP_TITLE
     *
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_sort_by_case_title()
    {
        $user = factory(User::class)->create();
        for ($x = 1; $x <= 5; $x++) {
            $list = factory(ListUnassigned::class)->states('foreign_keys')->create();
            factory(TaskUser::class)->create([
                'TAS_UID' => $list->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
        }
        //Define the filters
        $filters = ['sort' => 'APP_TITLE', 'dir' => 'ASC'];
        //Get data
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertGreaterThan($result[0]['APP_TITLE'], $result[1]['APP_TITLE']);

        //Define the filters
        $filters = ['sort' => 'APP_TITLE', 'dir' => 'DESC'];
        //Get data
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertGreaterThan($result[1]['APP_TITLE'], $result[0]['APP_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by case number APP_PRO_TITLE
     *
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_sort_by_process()
    {
        $user = factory(User::class)->create();
        for ($x = 1; $x <= 5; $x++) {
            $list = factory(ListUnassigned::class)->states('foreign_keys')->create();
            factory(TaskUser::class)->create([
                'TAS_UID' => $list->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
        }
        //Define the filters
        $filters = ['sort' => 'APP_PRO_TITLE', 'dir' => 'ASC'];
        //Get data
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertGreaterThan($result[0]['APP_PRO_TITLE'], $result[1]['APP_PRO_TITLE']);
        //Define the filters
        $filters = ['sort' => 'APP_PRO_TITLE', 'dir' => 'DESC'];
        //Get data
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertGreaterThan($result[1]['APP_PRO_TITLE'], $result[0]['APP_PRO_TITLE']);
    }

    /**
     * This ensures ordering ascending and descending works by case number APP_TAS_TITLE
     *
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_sort_by_task()
    {
        $user = factory(User::class)->create();
        for ($x = 1; $x <= 5; $x++) {
            $list = factory(ListUnassigned::class)->states('foreign_keys')->create();
            factory(TaskUser::class)->create([
                'TAS_UID' => $list->TAS_UID,
                'USR_UID' => $user->USR_UID,
                'TU_RELATION' => 1, //Related to the user
                'TU_TYPE' => 1
            ]);
        }
        //Define the filters
        $filters = ['sort' => 'APP_TAS_TITLE', 'dir' => 'ASC'];
        //Get data
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertGreaterThan($result[0]['APP_TAS_TITLE'], $result[1]['APP_TAS_TITLE']);
        //Define the filters
        $filters = ['sort' => 'APP_TAS_TITLE', 'dir' => 'DESC'];
        //Get data
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertGreaterThan($result[1]['APP_TAS_TITLE'], $result[0]['APP_TAS_TITLE']);
    }

    /**
     * This checks to make sure filter by category is working properly
     *
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_return_data_filtered_by_process_category()
    {
        //Create user
        $user = factory(User::class)->create();
        //Create a category
        $category = factory(ProcessCategory::class)->create();
        //Create process
        $process = factory(Process::class)->create([
            'PRO_CATEGORY' => $category->CATEGORY_UID
        ]);
        //Create a category
        $category1 = factory(ProcessCategory::class)->create();
        //Create process
        $process1 = factory(Process::class)->create([
            'PRO_CATEGORY' => $category1->CATEGORY_UID
        ]);
        //Create a task self service
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 2)->create([
            'TAS_ID' => $task->TAS_ID,
            'PRO_UID' => $process->PRO_UID,
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 5)->create([
            'TAS_ID' => $task->TAS_ID,
            'PRO_UID' => $process1->PRO_UID,
        ]);
        //Get all data
        $result = ListUnassigned::loadList($user->USR_UID);
        $this->assertCount(7, $result);
        //Define the filters
        $filters = ['category' => $category->CATEGORY_UID];
        //Get data
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Get the minor case number first
        $this->assertEquals($category->CATEGORY_UID, $result[0]['PRO_CATEGORY']);
        //Get the major case number second
        $this->assertEquals($category->CATEGORY_UID, $result[1]['PRO_CATEGORY']);
    }

    /**
     * This checks to make sure filter by category is working properly
     *
     * @covers ListUnassigned::loadList
     * @test
     */
    public function it_should_return_data_filtered_by_generic_search()
    {
        //Create user
        $user = factory(User::class)->create();
        //Create process
        $process = factory(Process::class)->create();
        //Create a task self service
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);
        //Assign a user in the task
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 2)->create([
            'TAS_ID' => $task->TAS_ID,
            'APP_TITLE' => 'This is a case name',
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 2)->create([
            'TAS_ID' => $task->TAS_ID,
            'APP_PRO_TITLE' => 'This is a process name',
        ]);
        //Create the register in list unassigned
        factory(ListUnassigned::class, 2)->create([
            'TAS_ID' => $task->TAS_ID,
            'APP_TAS_TITLE' => 'This is a task name',
        ]);
        //Create other registers
        factory(ListUnassigned::class, 4)->create([
            'TAS_ID' => $task->TAS_ID
        ]);
        //Define the filters
        $filters = ['search' => 'case name'];
        //Get data related to the search
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Define the filters
        $filters = ['search' => 'process name'];
        //Get data related to the search
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertCount(2, $result);
        //Define the filters
        $filters = ['search' => 'task name'];
        //Get data related to the search
        $result = ListUnassigned::loadList($user->USR_UID, $filters);
        $this->assertCount(2, $result);
    }
}

