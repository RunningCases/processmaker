<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessCategory;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * @coversDefaultClass ProcessMaker\BusinessModel\Model\Process
 */
class ProcessTest extends TestCase
{
    use DatabaseTransactions;

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
}