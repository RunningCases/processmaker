<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use ProcessMaker\Model\Process;
use ProcessMaker\Model\Triggers;
use Tests\TestCase;

class TriggersTest extends TestCase
{
    /**
     * It tests the process scope in the trigger model
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
}