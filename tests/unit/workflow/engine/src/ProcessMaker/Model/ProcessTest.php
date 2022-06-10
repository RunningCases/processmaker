<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Exception;
use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessCategory;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class ProcessTest
 *
 * @coversDefaultClass \ProcessMaker\Model\Process
 */
class ProcessTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Call the setUp parent method
     */
    public function setUp(): void
    {
        parent::setUp();
        Process::query()->delete();
    }

    /**
     * Test belongs to PRO_ID
     *
     * @covers \ProcessMaker\Model\Process::tasks()
     * @test
     */
    public function it_has_tasks()
    {
        $process = factory(Process::class)->create();
        factory(Task::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID
        ]);
        $this->assertInstanceOf(Task::class, $process->tasks);
    }

    /**
     * Test belongs to PRO_CREATE_USER
     *
     * @covers \ProcessMaker\Model\Process::creator()
     * @test
     */
    public function it_has_a_creator()
    {
        $process = factory(Process::class)->create([
            'PRO_CREATE_USER' => function () {
                return factory(User::class)->create()->USR_UID;
            }
        ]);
        $this->assertInstanceOf(User::class, $process->creator);
    }

    /**
     * Test belongs to PRO_CREATE_USER
     *
     * @covers \ProcessMaker\Model\Process::category()
     * @test
     */
    public function it_has_an_category()
    {
        $process = factory(Process::class)->create([
            'PRO_CATEGORY' => function () {
                return factory(ProcessCategory::class)->create()->CATEGORY_UID;
            }
        ]);
        $this->assertInstanceOf(ProcessCategory::class, $process->category);
    }

    /**
     * Test it returns all the processes for an specific user
     *
     * @covers \ProcessMaker\Model\Process::getProcessList()
     * @test
     */
    public function it_should_return_all_the_processes_for_an_specific_user()
    {
        //Create process
        $process = factory(Process::class, 2)->states('foreign_keys')->create([]);
        //Create a Process object
        $pro = new Process();
        //Call the getProcessList() method
        $res = $pro->getProcessList('', $process[0]->PRO_CREATE_USER);
        //Assert the result is not empty
        $this->assertNotEmpty($res);
        //Assert there's one result
        $this->assertCount(1, $res);
        //Assert that the process returned is the one looked for
        $this->assertEquals($process[0]->PRO_UID, $res[0]['PRO_UID']);
    }

    /**
     * Tests that it returns the processes in an specific category
     *
     * @covers \ProcessMaker\Model\Process::getProcessList()
     * @test
     */
    public function it_should_return_the_processes_in_an_specific_category()
    {
        $catUid1 = G::generateUniqueID();
        $catUid2 = G::generateUniqueID();

        //Create user
        $user = factory(User::class)->create();
        //Create process
        $process1 = factory(Process::class)->create([
            'PRO_CREATE_USER' => $user['USR_UID'],
            'PRO_CATEGORY' => $catUid1
        ]);
        $process2 = factory(Process::class)->create([
            'PRO_CREATE_USER' => $user['USR_UID'],
            'PRO_CATEGORY' => $catUid2
        ]);

        //Create a Process object
        $pro = new Process();
        //Call the getProcessList() method
        $res = $pro->getProcessList($process1['PRO_CATEGORY'], $user['USR_UID']);

        //Assert the result is not empty
        $this->assertNotEmpty($res);
        //Assert there's one result
        $this->assertCount(1, $res);
        //Assert that the process returned belong to the category searched
        $this->assertEquals($process1['PRO_UID'], $res[0]['PRO_UID']);
        //Assert the process which their category was not searched is not in the result
        $this->assertNotEquals($process2['PRO_UID'], $res[0]['PRO_UID']);
    }

    /**
     * Tests that it returns an empty array if no processes where found
     *
     * @covers \ProcessMaker\Model\Process::getProcessList()
     * @test
     */
    public function it_should_return_empty_if_no_processes_where_found()
    {
        //Create user
        $user = factory(User::class)->create();
        //Create a Process object
        $process = new Process();
        //Call the getProcessList() method
        $res = $process->getProcessList('', $user['USR_UID']);

        //Assert the result is not empty
        $this->assertEmpty($res);
    }

    /**
     * Test it returns all the processes in status active
     *
     * @covers \ProcessMaker\Model\Process::getProcessList()
     * @test
     */
    public function it_should_return_all_the_processes_in_status_active()
    {
        //Create user
        $user = factory(User::class)->create();
        //Create process
        $process1 = factory(Process::class)->create([
            'PRO_CREATE_USER' => $user['USR_UID'],
            'PRO_STATUS' => 'ACTIVE'
        ]);
        $process2 = factory(Process::class)->create([
            'PRO_CREATE_USER' => $user['USR_UID'],
            'PRO_STATUS' => 'INACTIVE'
        ]);
        $process3 = factory(Process::class)->create([
            'PRO_CREATE_USER' => $user['USR_UID'],
            'PRO_STATUS' => 'DISABLED'
        ]);

        //Create a Process object
        $process = new Process();
        //Call the getProcessList() method
        $res = $process->getProcessList('', $user['USR_UID']);

        //Assert the result is not empty
        $this->assertNotEmpty($res);
        //Assert there's one result
        $this->assertCount(1, $res);
        //Assert that the process returned is the one that has ACTIVE status
        $this->assertEquals($process1['PRO_UID'], $res[0]['PRO_UID']);
        //Assert the processes that have not ACTIVE status are not in the result
        $this->assertNotEquals($process2['PRO_UID'], $res[0]['PRO_UID']);
        $this->assertNotEquals($process3['PRO_UID'], $res[0]['PRO_UID']);
    }

    /**
     * It tests the getProcessPrivateListByUser method
     * 
     * @covers \ProcessMaker\Model\Process::getProcessPrivateListByUser()
     * @test
     */
    public function it_should_test_the_get_process_private_list_by_user_method()
    {
        //Create user
        $user = factory(User::class)->create();

        //Create process
        factory(Process::class)->create([
            'PRO_CREATE_USER' => $user['USR_UID'],
            'PRO_STATUS' => 'ACTIVE',
            'PRO_TYPE_PROCESS' => 'PRIVATE',
        ]);

        //Create a Process object
        $process = new Process();

        //Call the getProcessPrivateListByUser() method
        $res = $process->getProcessPrivateListByUser($user['USR_UID']);

        // This asserts the result contains one row
        $this->assertCount(1, $res);
    }

    /**
     * It tests the convertPrivateProcessesToPublicAndUpdateUser method
     * 
     * @covers \ProcessMaker\Model\Process::convertPrivateProcessesToPublicAndUpdateUser()
     * @test
     */
    public function it_should_test_the_convert_private_processes_to_public_method()
    {
        //Create user
        $user = factory(User::class)->create();

        //Create process
        $pro = factory(Process::class)->create([
            'PRO_CREATE_USER' => $user['USR_UID'],
            'PRO_STATUS' => 'ACTIVE',
            'PRO_TYPE_PROCESS' => 'PRIVATE',
        ]);

        $p = Process::where('PRO_UID', $pro->PRO_UID)->get()->values()->toArray();
        //Create a Process object
        $process = new Process();

        //Call the convertPrivateProcessesToPublicAndUpdateUser() method
        $process->convertPrivateProcessesToPublicAndUpdateUser($p, $pro->PRO_CREATE_USER);

        $p = Process::where('PRO_UID', $pro->PRO_UID)->get()->values();

        // This asserts the process was converted from private to public
        $this->assertEquals('PUBLIC', $p[0]->PRO_TYPE_PROCESS);
    }

     /**
     * Test the exception
     *
     * @covers \ProcessMaker\Model\Process::getProcessesFilter()
     * @test
     */
    public function it_test_exception_get_process()
    {
        $this->expectException(Exception::class);
        $result = Process::getProcessesFilter(
            null,
            null,
            null,
            null,
            0,
            25,
            'ASC',
            'OTHER_COLUMN_DOES_NOT_EXIST'
        );
    }

    /**
     * It tests the process list
     *
     * @covers \ProcessMaker\Model\Process::getProcessesFilter()
     * @covers \ProcessMaker\Model\Process::getListColumns()
     * @covers \ProcessMaker\Model\Process::scopeJoinUsers()
     * @covers \ProcessMaker\Model\Process::scopeJoinCategory()
     * @covers \ProcessMaker\Model\Process::scopeNoStatus()
     * @test
     */
    public function it_should_test_process_without_filter()
    {
        $process = factory(Process::class)->create();
        $result = Process::getProcessesFilter(
            null,
            null,
            null,
            $process->PRO_CREATE_USER
        );
        $this->assertEquals($process->PRO_CREATE_USER, $result[0]['USR_UID']);
    }

    /**
     * It tests the process list with specific category
     *
     * @covers \ProcessMaker\Model\Process::getProcessesFilter()
     * @covers \ProcessMaker\Model\Process::getListColumns()
     * @covers \ProcessMaker\Model\Process::scopeJoinUsers()
     * @covers \ProcessMaker\Model\Process::scopeJoinCategory()
     * @covers \ProcessMaker\Model\Process::scopeCategory()
     * @covers \ProcessMaker\Model\Process::scopePerUser()
     * @test
     */
    public function it_should_test_process_with_category_filter()
    {
        $process = factory(Process::class)->create([
            'PRO_CATEGORY' => function () {
                return factory(ProcessCategory::class)->create()->CATEGORY_UID;
            }
        ]);
        $result = Process::getProcessesFilter($process->PRO_CATEGORY);
        // Assert with the specific category
        $this->assertEquals($process->PRO_CATEGORY, $result[0]['PRO_CATEGORY']);

        $process = factory(Process::class)->create();
        $result = Process::getProcessesFilter('NONE');
        // Assert when the category is empty
        $this->assertEmpty($result);
    }

    /**
     * It tests the process list with specific process
     *
     * @covers \ProcessMaker\Model\Process::getProcessesFilter()
     * @covers \ProcessMaker\Model\Process::getListColumns()
     * @covers \ProcessMaker\Model\Process::scopeJoinUsers()
     * @covers \ProcessMaker\Model\Process::scopeJoinCategory()
     * @covers \ProcessMaker\Model\Process::scopeProcess()
     * @test
     */
    public function it_should_test_process_with_process_filter()
    {
        $process = factory(Process::class)->create();
        $result = Process::getProcessesFilter(
            null,
            $process->PRO_UID
        );
        $this->assertEquals($process->PRO_UID, $result[0]['PRO_UID']);
    }

    /**
     * It tests the process list with specific process title
     *
     * @covers \ProcessMaker\Model\Process::getProcessesFilter()
     * @covers \ProcessMaker\Model\Process::getListColumns()
     * @covers \ProcessMaker\Model\Process::scopeJoinUsers()
     * @covers \ProcessMaker\Model\Process::scopeJoinCategory()
     * @covers \ProcessMaker\Model\Process::scopeTitle()
     * @test
     */
    public function it_should_test_process_with_title_filter()
    {
        $process = factory(Process::class)->create();
        $result = Process::getProcessesFilter(
            null,
            null,
            $process->PRO_TITLE
        );
        $this->assertEquals($process->PRO_TITLE, $result[0]['PRO_TITLE']);
    }

    /**
     * It tests the process list with suprocess filter
     *
     * @covers \ProcessMaker\Model\Process::getProcessesFilter()
     * @covers \ProcessMaker\Model\Process::getListColumns()
     * @covers \ProcessMaker\Model\Process::scopeJoinUsers()
     * @covers \ProcessMaker\Model\Process::scopeJoinCategory()
     * @covers \ProcessMaker\Model\Process::scopeSubProcess()
     * @test
     */
    public function it_should_test_process_subprocess_filter()
    {
        $process = factory(Process::class)->create([
            'PRO_SUBPROCESS' => 1
        ]);
        $result = Process::getProcessesFilter(
            null,
            null,
            null,
            $process->PRO_CREATE_USER,
            0,
            25,
            'ASC',
            'PRO_CREATE_DATE',
            true,
            true
        );
        $this->assertEquals($process->PRO_CREATE_USER, $result[0]['USR_UID']);
    }

    /**
     * It tests the count process
     *
     * @covers \ProcessMaker\Model\Process::getCounter()
     * @covers \ProcessMaker\Model\Process::scopePerUser()
     * @test
     */
    public function it_should_test_count_process()
    {
        $process = factory(Process::class)->create();
        $total = Process::getCounter($process->PRO_CREATE_USER);
        $this->assertEquals(1, $total);
    }

    /**
     * It test get processes for the new home view
     *
     * @covers \ProcessMaker\Model\Process::getProcessesForHome()
     * @covers \ProcessMaker\Model\Process::scopeCategoryId()
     * @covers \ProcessMaker\Model\Process::scopeStatus()
     * @test
     */
    public function it_should_test_get_processes_for_home()
    {
        // Create a process category
        $processCategory = factory(ProcessCategory::class)->create();

        // Create five processes (4 active, 1 inactive)
        factory(Process::class)->create([
            'PRO_TITLE' => 'My Process 1',
            'PRO_CATEGORY' => $processCategory->CATEGORY_UID,
            'CATEGORY_ID' => $processCategory->CATEGORY_ID
        ]);
        factory(Process::class)->create([
            'PRO_TITLE' => 'My Process 2',
            'PRO_CATEGORY' => $processCategory->CATEGORY_UID,
            'CATEGORY_ID' => $processCategory->CATEGORY_ID
        ]);
        factory(Process::class)->create([
            'PRO_TITLE' => 'My Process 3',
        ]);
        factory(Process::class)->create([
            'PRO_TITLE' => 'Another Process',
        ]);
        factory(Process::class)->create([
            'PRO_TITLE' => 'Inactive Process',
            'PRO_STATUS' => 'INACTIVE'
        ]);

        // Assertions
        $this->assertCount(4, Process::getProcessesForHome());
        $this->assertCount(3, Process::getProcessesForHome('My Process'));
        $this->assertCount(2, Process::getProcessesForHome(null, $processCategory->CATEGORY_ID));
        $this->assertCount(4, Process::getProcessesForHome(null, null, null, 2));
        $this->assertCount(1, Process::getProcessesForHome(null, null, 2, 1, true));
    }

    /**
     * It test get id
     *
     * @covers \ProcessMaker\Model\Process::getIds()
     * @test
     */
    public function it_should_test_get_id()
    {
        $process = factory(Process::class)->create();
        $result = Process::getIds($process->PRO_UID, 'PRO_UID');
        $this->assertEquals($process->PRO_ID, $result[0]['PRO_ID']);
    }

    /**
     * It tests the isActive process
     *
     * @covers \ProcessMaker\Model\Process::isActive()
     * @test
     */
    public function it_should_test_is_active()
    {
        $process = factory(Process::class)->create();
        $total = Process::isActive($process->PRO_ID);
        $this->assertEquals(1, $total);
    }
}
