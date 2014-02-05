<?php
namespace Tests\ProcessMaker\Project;

use \ProcessMaker\Project;

if (! class_exists("Propel")) {
    include_once __DIR__ . "/../bootstrap.php";
}

class ProcessMakerProjectWorkflowTest extends \PHPUnit_Framework_TestCase
{
    protected static $proUids = array();

    public static function tearDownAfterClass()
    {
        //cleaning DB
        foreach (self::$proUids as $proUid) {
            $wp = Project\Workflow::load($proUid);
            $wp->remove();
        }
    }

    public function testCreate()
    {
        $data = array(
            "PRO_TITLE" => "Test Project #1",
            "PRO_DESCRIPTION" => "Description for - Test Project #1",
            "PRO_CATEGORY" => "",
            "PRO_CREATE_USER" => "00000000000000000000000000000001"
        );

        $wp = new Project\Workflow($data);
        self::$proUids[] = $wp->getUid();

        $processData = $wp->getProcess();

        foreach ($data as $key => $value) {
            $this->assertEquals($data[$key], $processData[$key]);
        }

        return $wp;
    }

    /**
     * @depends testCreate
     */
    public function testAddTask($wp)
    {
        $data = array(
            "TAS_TITLE" => "task #1",
            "TAS_DESCRIPTION" => "Description for task #1",
            "TAS_POSX" => "50",
            "TAS_POSY" => "50",
            "TAS_WIDTH" => "100",
            "TAS_HEIGHT" => "25"
        );

        $tasUid = $wp->addTask($data);

        $taskData = $wp->getTask($tasUid);

        foreach ($data as $key => $value) {
            $this->assertEquals($data[$key], $taskData[$key]);
        }
    }

    /**
     * @depends testCreate
     */
    public function testUpdateTask($wp)
    {
        $data = array(
            "TAS_TITLE" => "task #1 (updated)",
            "TAS_POSX" => "150",
            "TAS_POSY" => "250"
        );

        // at this time, there is only one task
        $tasks = $wp->getTasks();
        $this->assertInternalType('array', $tasks);
        $this->assertCount(1, $tasks);

        $wp->updateTask($tasks[0]['TAS_UID'], $data);
        $taskData = $wp->getTask($tasks[0]['TAS_UID']);

        foreach ($data as $key => $value) {
            $this->assertEquals($data[$key], $taskData[$key]);
        }
    }

    /**
     * @depends testCreate
     */
    public function testRemoveTask($wp)
    {
        $tasUid = $wp->addTask(array(
            "TAS_TITLE" => "task #2",
            "TAS_POSX" => "150",
            "TAS_POSY" => "250"
        ));

        $tasks = $wp->getTasks();
        $this->assertInternalType('array', $tasks);
        $this->assertCount(2, $tasks);

        $wp->removeTask($tasUid);

        $tasks = $wp->getTasks();
        $this->assertInternalType('array', $tasks);
        $this->assertCount(1, $tasks);
    }

    /**
     * @depends testCreate
     */
    public function testGetTasks($wp)
    {
        $tasUid1 = $wp->addTask(array(
            "TAS_TITLE" => "task #2",
            "TAS_POSX" => "250",
            "TAS_POSY" => "250"
        ));
        $tasUid2 = $wp->addTask(array(
            "TAS_TITLE" => "task #3",
            "TAS_POSX" => "350",
            "TAS_POSY" => "350"
        ));

        $tasks = $wp->getTasks();
        $this->assertInternalType('array', $tasks);
        $this->assertCount(3, $tasks);

        $wp->removeTask($tasUid1);

        $tasks = $wp->getTasks();
        $this->assertInternalType('array', $tasks);
        $this->assertCount(2, $tasks);

        $wp->removeTask($tasUid2);

        $tasks = $wp->getTasks();
        $this->assertInternalType('array', $tasks);
        $this->assertCount(1, $tasks);

        $wp->removeTask($tasks[0]['TAS_UID']);

        $tasks = $wp->getTasks();
        $this->assertInternalType('array', $tasks);
        $this->assertCount(0, $tasks);
    }

    /**
     *
     */
    public function testAddRoute()
    {
        $wp = new Project\Workflow(array(
            "PRO_TITLE" => "Test Project #2 (Sequential)",
            "PRO_CREATE_USER" => "00000000000000000000000000000001"
        ));

        self::$proUids[] = $wp->getUid();

        $tasUid1 = $wp->addTask(array(
            "TAS_TITLE" => "task #1",
            "TAS_POSX" => "410",
            "TAS_POSY" => "61"
        ));
        $tasUid2 = $wp->addTask(array(
            "TAS_TITLE" => "task #2",
            "TAS_POSX" => "159",
            "TAS_POSY" => "370"
        ));

        $rouUid = $wp->addRoute($tasUid1, $tasUid2, "SEQUENTIAL");

        $routeSaved = $wp->getRoute($rouUid);

        $this->assertEquals($tasUid1, $routeSaved['TAS_UID']);
        $this->assertEquals($tasUid2, $routeSaved['ROU_NEXT_TASK']);
        $this->assertEquals("SEQUENTIAL", $routeSaved['ROU_TYPE']);
    }

    public function testAddSelectRoute()
    {
        $wp = new Project\Workflow(array(
            "PRO_TITLE" => "Test Project #3 (Select)",
            "PRO_CREATE_USER" => "00000000000000000000000000000001"
        ));
        self::$proUids[] = $wp->getUid();

        $tasUid1 = $wp->addTask(array(
            "TAS_TITLE" => "task #1",
            "TAS_POSX" => "410",
            "TAS_POSY" => "61"
        ));
        $tasUid2 = $wp->addTask(array(
            "TAS_TITLE" => "task #2",
            "TAS_POSX" => "159",
            "TAS_POSY" => "370"
        ));
        $tasUid3 = $wp->addTask(array(
            "TAS_TITLE" => "task #3",
            "TAS_POSX" => "670",
            "TAS_POSY" => "372"
        ));

        $wp->addSelectRoute($tasUid1, array($tasUid2, $tasUid3));
    }

    public function testCompleteWorkflowProject()
    {
        $wp = new Project\Workflow(array(
            "PRO_TITLE" => "Test Complete Project #4",
            "PRO_CREATE_USER" => "00000000000000000000000000000001"
        ));

        $tasUid1 = $wp->addTask(array(
            "TAS_TITLE" => "task #1",
            "TAS_POSX" => "406",
            "TAS_POSY" => "71"
        ));
        $tasUid2 = $wp->addTask(array(
            "TAS_TITLE" => "task #2",
            "TAS_POSX" => "188",
            "TAS_POSY" => "240"
        ));
        $tasUid3 = $wp->addTask(array(
            "TAS_TITLE" => "task #3",
            "TAS_POSX" => "406",
            "TAS_POSY" => "239"
        ));
        $tasUid4 = $wp->addTask(array(
            "TAS_TITLE" => "task #4",
            "TAS_POSX" => "294",
            "TAS_POSY" => "366"
        ));
        $tasUid5 = $wp->addTask(array(
            "TAS_TITLE" => "task #5",
            "TAS_POSX" => "640",
            "TAS_POSY" => "240"
        ));
        $tasUid6 = $wp->addTask(array(
            "TAS_TITLE" => "task #6",
            "TAS_POSX" => "640",
            "TAS_POSY" => "359"
        ));


        $wp->addRoute($tasUid1, $tasUid2, "PARALLEL");
        $wp->addRoute($tasUid1, $tasUid3, "PARALLEL");
        $wp->addRoute($tasUid1, $tasUid5, "PARALLEL");

        $wp->addRoute($tasUid2, $tasUid4, "SEC-JOIN");
        $wp->addRoute($tasUid3, $tasUid4, "SEC-JOIN");

        $wp->addRoute($tasUid5, $tasUid6, "EVALUATE");
        $wp->addRoute($tasUid5, "-1", "EVALUATE");

        $wp->setStartTask($tasUid1);

        $wp->setEndTask($tasUid4);
        $wp->setEndTask($tasUid6);

        return $wp;
    }

    /**
     * @depends testCompleteWorkflowProject
     * @param $wp \ProcessMaker\Project\WorkflowProject
     * @expectedException \ProcessMaker\Exception\ProjectNotFound
     * @expectedExceptionCode 20
     */
    public function testRemove($wp)
    {
        $proUid = $wp->getUid();
        $wp->remove();

        Project\Workflow::load($proUid);
    }
}