<?php

namespace ProcessMaker\BusinessModel;

use ProcessMaker\BusinessModel\TaskSchedulerBM;
use ProcessMaker\Model\TaskScheduler;
use Tests\TestCase;

class TaskSchedulerBMTest extends TestCase
{
    /**
     * Test getSchedule method
     * 
     * @covers \ProcessMaker\BusinessModel\TaskSchedulerBM::getSchedule
     * @test
     */
    public function it_should_test_get_schedule_method()
    {
        TaskScheduler::truncate();
        $obj = new TaskSchedulerBM();
        
        $res = $obj->getSchedule('emails_notifications');
        $this->assertNotEmpty($res);

        factory(TaskScheduler::class)->create();

        $res = $obj->getSchedule('emails_notifications');
        $this->assertNotEmpty(1, $res);

        $res = $obj->getSchedule('case_actions');
        $this->assertEmpty(0, $res);

        $res = $obj->getSchedule('plugins');
        $this->assertEmpty(0, $res);

        $res = $obj->getSchedule('processmaker_sync');
        $this->assertEmpty(0, $res);

        $res = $obj->getSchedule(null);
        $this->assertNotEmpty(1, $res);
    }

    /**
     * Test saveSchedule method
     * 
     * @covers \ProcessMaker\BusinessModel\TaskSchedulerBM::saveSchedule
     * @test
     */
    public function it_should_test_save_schedule_method()
    {
        $obj = new TaskSchedulerBM();
        
        $scheduler = factory(TaskScheduler::class)->create();

        $request_data = [
            "id" => $scheduler->id,
            "title" => "ProcessMaker Events",
            "enable" => "1",
            "service" => "events",
            "category" => "case_actions",
            "file" => "workflow/engine/bin/cron.php",
            "startingTime" => "0:00",
            "endingTime" => "23:59",
            "expression" => "* * * * *",
            "description" => "Unpauses any case whose pause time has expired",
            "timezone" => "",
            "everyOn" => "",
            'interval' => "",
            'body' => "",
            'type' => "",
            'system' => "",
            'creation_date' => date(''),
            'last_update' => date('') 
        ];

        $res = $obj->saveSchedule($request_data);
        $this->assertEquals($scheduler->id , $res->id);
    }

    /**
     * Test generateInitialData method
     * 
     * @covers \ProcessMaker\BusinessModel\TaskSchedulerBM::generateInitialData
     * @test
     */
    public function it_should_test_generate_initial_data_method()
    {
        TaskScheduler::truncate();
        $r = TaskScheduler::all()->toArray();
        $this->assertEmpty($r);

        $obj = new TaskSchedulerBM();
        $obj->generateInitialData();

        $r = TaskScheduler::all()->toArray();
        $this->assertNotEmpty($r);
    }
}
