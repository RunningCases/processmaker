<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Triggers;
use Tests\TestCase;

/**
 * Class TriggersTest
 *
 * @coversDefaultClass \ProcessMaker\Model\Triggers
 */
class TriggersTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test set and get the trigger property
     *
     * @covers \ProcessMaker\Model\Triggers::setTrigger()
     * @covers \ProcessMaker\Model\Triggers::getTrigger()
     * @test
     */
    public function it_set_get_trigger()
    {
        factory(Triggers::class)->create();
        $trigger = factory(Triggers::class)->create();
        $trigger->setTrigger($trigger->TRI_UID);
        $this->assertEquals($trigger->getTrigger(), $trigger->TRI_UID);
    }

    /**
     * It tests the process scope in the trigger model
     *
     * @covers \ProcessMaker\Model\Triggers::scopeProcess()
     * @test
     */
    public function it_should_test_process_scope_in_trigger_model()
    {
        $process = factory(Process::class, 3)->create();
        factory(Triggers::class)->create(
            [
                'PRO_UID' => $process[0]['PRO_UID'],
                'TRI_WEBBOT' => '$text=222;
                                $var1= executeQuery("SELECT * 
                                FROM USERS WHERE 
                                USR_UID=\'$UID\' UNION SELECT * from PROCESS");
                                
                                $var1= executeQuery("SELECT * 
                                FROM USERS WHERE 
                                USR_UID=\'$UID\' UNION SELECT * from PROCESS");
                                
                                $query = "SELECT * FROM USERS UNION 
                                
                                SELECT * FROM TASKS";
                                
                                $QUERY2 = "select * from USERS union SELECT * from GROUPS";
                                
                                $s1 = "select * from USER";
                                $s2 = "select * from TASK";
                                
                                $query3 = $s1. " UNION " . $s2;
                                
                                executeQuery($query3);'
            ]
        );

        factory(Triggers::class)->create(
            [
                'PRO_UID' => $process[1]['PRO_UID'],
                'TRI_WEBBOT' => 'die();'
            ]
        );

        factory(Triggers::class)->create(
            [
                'PRO_UID' => $process[2]['PRO_UID'],
                'TRI_WEBBOT' => 'executeQuery("select * from USERS");'
            ]
        );

        factory(Triggers::class)->create(
            [
                'PRO_UID' => $process[2]['PRO_UID'],
                'TRI_WEBBOT' => 'executeQuery();'
            ]
        );

        $triggerQuery = Triggers::query()->select();
        $triggerQuery->process($process[2]['PRO_UID']);
        $result = $triggerQuery->get()->values()->toArray();

        // Assert there are two triggers for the specific process
        $this->assertCount(2, $result);

        // Assert that the result has the correct filtered process
        $this->assertEquals($process[2]['PRO_UID'], $result[0]['PRO_UID']);
        $this->assertEquals($process[2]['PRO_UID'], $result[1]['PRO_UID']);
    }

    /**
     * Test scope a query to only include a specific case
     *
     * @covers \ProcessMaker\Model\Triggers::scopeTrigger()
     * @test
     */
    public function it_filter_specific_tasks()
    {
        factory(Triggers::class)->create();
        $trigger = factory(Triggers::class)->create();
        $this->assertCount(1, $trigger->trigger($trigger->TRI_UID)->get());
    }

    /**
     * This checks it returns information about the trigger
     *
     * @covers \ProcessMaker\Model\Triggers::triggers()
     * @test
     */
    public function it_return_specific_trigger_information()
    {
        $triggers = factory(Triggers::class, 5)->create();
        $trigger = new Triggers();
        $trigger->setTrigger($triggers[0]->TRI_UID);
        $triggersList = $trigger->triggers();
        $this->assertCount(1, $triggersList);
        $this->assertEquals($triggers[0]->TRI_TITLE , $triggersList[0]['TRI_TITLE']);
    }

    /**
     * This checks it returns empty when the trigger does not exist
     *
     * @covers \ProcessMaker\Model\Triggers::triggers()
     * @test
     */
    public function it_return_empty_when_the_trigger_not_exist()
    {
        factory(Triggers::class)->create();
        $trigger = new Triggers();
        $trigger->setTrigger(G::generateUniqueID());
        $triggersList = $trigger->triggers();
        $this->assertEmpty($triggersList);
    }
}
