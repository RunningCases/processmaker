<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\BusinessModel\Cases\Reassign;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Reassign
 */
class ReassignTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * It tests the getData method without filters
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Reassign::getData()
     * @test
     */
    public function it_should_test_get_data_method_without_filters()
    {
        $cases = 25;
        factory(Delegation::class, $cases)->states('foreign_keys')->create();
        //Create new Reassign object
        $reassign = new Reassign();
        //Set OrderBYColumn value
        $reassign->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        //Call to getData method
        $res = $reassign->getData();
        //This assert that the expected numbers of results are returned
        $this->assertEquals($cases, count($res));
    }

    /**
     * It tests the getData method with user filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Reassign::getData()
     * @test
     */
    public function it_should_test_get_data_method_with_user_filter()
    {
        // Create user
        $user = factory(User::class)->create();
        // Create delegation related to the specific user
        factory(Delegation::class)->states('foreign_keys')->create([
            'USR_ID' => $user->USR_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        // Create other delegations
        $cases = 5;
        factory(Delegation::class, $cases)->states('foreign_keys')->create();
        //Create new Reassign object
        $reassign = new Reassign();
        //Set the user UID
        $reassign->setUserUid($user->USR_UID);
        //Set the user ID
        $reassign->setUserId($user->USR_ID);
        //Set OrderBYColumn value
        $reassign->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        //Call to getData method
        $res = $reassign->getData();
        //This assert that the expected numbers of results are returned
        $this->assertEquals(1, count($res));
    }

    /**
     * It tests the getData method with process filter
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Reassign::getData()
     * @test
     */
    public function it_should_test_get_data_method_with_process_filter()
    {
        // Create user
        $process = factory(Process::class)->create();
        // Create delegation related to the specific user
        factory(Delegation::class)->states('foreign_keys')->create([
            'PRO_ID' => $process->PRO_ID,
            'PRO_UID' => $process->PRO_UID,
        ]);
        // Create other delegations
        $cases = 5;
        factory(Delegation::class, $cases)->states('foreign_keys')->create();
        //Create new Reassign object
        $reassign = new Reassign();
        //Set the process
        $reassign->setProcessId($process->PRO_ID);
        $reassign->setProcessUid($process->PRO_UID);
        //Set OrderBYColumn value
        $reassign->setOrderByColumn('APP_DELEGATION.APP_NUMBER');
        //Call to getData method
        $res = $reassign->getData();
        //This assert that the expected numbers of results are returned
        $this->assertEquals(1, count($res));
    }

    /**
     * It tests the getCounter method
     *
     * @covers \ProcessMaker\BusinessModel\Cases\Reassign::getCounter()
     * @test
     */
    public function it_should_test_the_counter_for_reassign()
    {
        // Create user
        $user = factory(User::class)->create();
        $cases = 25;
        factory(Delegation::class, $cases)->states('foreign_keys')->create([
            'USR_ID' => $user->USR_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        //Create the Inbox object
        $reassign = new Reassign();
        //Set the user UID
        $reassign->setUserUid($user->USR_UID);
        //Set the user ID
        $reassign->setUserId($user->USR_ID);
        $res = $reassign->getCounter();
        //Assert the result of getCounter method
        $this->assertEquals($cases, $res);
    }
}