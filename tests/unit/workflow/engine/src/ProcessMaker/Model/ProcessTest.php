<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * @coversDefaultClass ProcessMaker\BusinessModel\Model\Process
 */

class ProcessTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test it returns all the processes for an specific user
     * @covers ::getProcessList
     * @test
     */
    public function it_should_return_all_the_processes_for_an_specific_user()
    {
        //Create user
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        //Create process
        $process1 = factory(Process::class)->create(
            ['PRO_CREATE_USER' => $user1['USR_UID']]
        );
        $process2 = factory(Process::class)->create(
            ['PRO_CREATE_USER' => $user2['USR_UID']]
        );

        //Create a Process object
        $process = new Process();
        //Call the getProcessList() method
        $res = $process->getProcessList('', $user2['USR_UID']);

        //Assert the result is not empty
        $this->assertNotEmpty($res);
        //Assert there's one result
        $this->assertCount(1, $res);
        //Assert that the process returned is the one looked for
        $this->assertEquals($process2['PRO_UID'], $res[0]['PRO_UID']);
        //Assert the process that was not searched is not in the result
        $this->assertNotEquals($process1['PRO_UID'], $res[0]['PRO_UID']);
    }

    /**
     * Tests that it returns the processes in an specific category
     * @covers ::getProcessList
     * @test
     */
    public function it_should_return_the_processes_in_an_specific_category()
    {
        $catUid1 = G::generateUniqueID();
        $catUid2 = G::generateUniqueID();

        //Create user
        $user = factory(User::class)->create();
        //Create process
        $process1 = factory(Process::class)->create(
            [
                'PRO_CREATE_USER' => $user['USR_UID'],
                'PRO_CATEGORY' => $catUid1
            ]
        );
        $process2 = factory(Process::class)->create(
            [
                'PRO_CREATE_USER' => $user['USR_UID'],
                'PRO_CATEGORY' => $catUid2
            ]
        );

        //Create a Process object
        $process = new Process();
        //Call the getProcessList() method
        $res = $process->getProcessList($process1['PRO_CATEGORY'], $user['USR_UID']);

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
     * @covers ::getProcessList
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
     * @covers ::getProcessList
     * @test
     */
    public function it_should_return_all_the_processes_in_status_active()
    {
        //Create user
        $user = factory(User::class)->create();
        //Create process
        $process1 = factory(Process::class)->create(
            [
                'PRO_CREATE_USER' => $user['USR_UID'],
                'PRO_STATUS' => 'ACTIVE'
            ]
        );
        $process2 = factory(Process::class)->create(
            [
                'PRO_CREATE_USER' => $user['USR_UID'],
                'PRO_STATUS' => 'INACTIVE'
            ]
        );
        $process3 = factory(Process::class)->create(
            [
                'PRO_CREATE_USER' => $user['USR_UID'],
                'PRO_STATUS' => 'DISABLED'
            ]
        );

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