<?php

if (! class_exists("Propel")) {
    include_once __DIR__ . "/../bootstrap.php";
}

use \ProcessMaker\Project\BpmnProject;
use \PHPUnit_Framework_TestCase;


class BpmnProjectTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $data = array(
            "PRJ_NAME" => "Test BPMN Project #1",
            "PRJ_DESCRIPTION" => "Description for - Test BPMN Project #1",
            "PRJ_AUTHOR" => "00000000000000000000000000000001"
        );

        // Create a new BpmnProject and save to DB
        $bp = new BpmnProject($data);
        $projectData = $bp->getProject();

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $projectData[$key]);
        }

        return $bp;
    }

    /**
     * @depends testCreate
     * @var $bp \ProcessMaker\Project\BpmnProject
     */
    public function testAddDiagram($bp)
    {
        $data = array(
            "DIA_NAME" => "Sample Diagram #1"
        );

        // Save to DB
        $bp->addDiagram($data);

        // Load from DB
        $diagramData = $bp->getDiagram();

        $this->assertEquals($data["DIA_NAME"], $diagramData["DIA_NAME"]);
        $this->assertEquals($bp->getUid(), $diagramData["PRJ_UID"]);
    }

    /**
     * @depends testCreate
     * @var $bp \ProcessMaker\Project\BpmnProject
     */
    public function testAddProcess($bp)
    {
        $data = array(
            "PRO_NAME" => "Sample Process #1"
        );

        $diagramData = $bp->getDiagram();

        // Save to DB
        $bp->addProcess($data);

        // Load from DB
        $processData = $bp->getProcess();

        $this->assertEquals($data["PRO_NAME"], $processData["PRO_NAME"]);
        $this->assertEquals($bp->getUid(), $processData["PRJ_UID"]);
        $this->assertEquals($diagramData['DIA_UID'], $processData["DIA_UID"]);
    }

    /**
     * @depends testCreate
     * @var $bp \ProcessMaker\Project\BpmnProject
     */
    public function testAddActivity($bp)
    {
        $data = array(
            "ACT_NAME" => "Activity #1",
            "BOU_X" => "50",
            "BOU_Y" => "50"
        );

        // Save to DB
        $bp->addActivity($data);

        // Load from DB
        $processData = $bp->getProcess();
        $activities = $bp->getActivities();

        $this->assertCount(1, $activities);

        $activityData = $activities[0];

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $activityData[$key]);
        }

        $this->assertEquals($bp->getUid(), $activityData["PRJ_UID"]);
        $this->assertEquals($processData["PRO_UID"], $activityData["PRO_UID"]);
    }

    /**
     * @depends testCreate
     * @param $bp \ProcessMaker\Project\BpmnProject
     * @return array
     */
    public function testAddActivityWithUid($bp)
    {
        $actUid = "f1198ddc864204561817155064020352";

        $data = array(
            "ACT_UID" => $actUid,
            "ACT_NAME" => "Activity #X",
            "BOU_X" => "50",
            "BOU_Y" => "50"
        );

        // Save to DB
        $bp->addActivity($data);

        // Load from DB
        $activities = $bp->getActivities();

        $uids = array();

        foreach ($activities as $activity) {
            array_push($uids, $activity["ACT_UID"]);
        }

        $this->assertTrue(in_array($actUid, $uids));

        return $data;
    }

    /**
     * @depends testCreate
     * @depends testAddActivityWithUid
     * @param $bp \ProcessMaker\Project\BpmnProject
     * @param $data
     */
    public function testGetActivity($bp, $data)
    {
        // Load from DB
        $activityData = $bp->getActivity($data["ACT_UID"]);

        // in data is set the determinated UID for activity created in previous step
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $activityData[$key]);
        }

        // Testing with an invalid uid
        $this->assertNull($bp->getActivity("INVALID-UID"));
    }

    /**
     * @depends testCreate
     * @depends testAddActivityWithUid
     * @param $bp \ProcessMaker\Project\BpmnProject
     * @param $data
     */
    public function testRemoveActivity($bp, $data)
    {
        $this->assertCount(2, $bp->getActivities());

        $bp->removeActivity($data["ACT_UID"]);

        $this->assertCount(1, $bp->getActivities());
    }

    public function testGetActivities()
    {
        // Create a new BpmnProject and save to DB
        $bp = new BpmnProject(array(
            "PRJ_NAME" => "Test BPMN Project #2",
            "PRJ_DESCRIPTION" => "Description for - Test BPMN Project #1",
            "PRJ_AUTHOR" => "00000000000000000000000000000001"
        ));
        $bp->addDiagram();
        $bp->addProcess();

        $this->assertCount(0, $bp->getActivities());

        // Save to DB
        $bp->addActivity(array(
            "ACT_NAME" => "Activity #2",
            "BOU_X" => "50",
            "BOU_Y" => "50"
        ));

        $bp->addActivity(array(
            "ACT_NAME" => "Activity #3",
            "BOU_X" => "50",
            "BOU_Y" => "50"
        ));

        $this->assertCount(2, $bp->getActivities());

        return $bp;
    }

    /**
     * @depends testGetActivities
     * @param $bp \ProcessMaker\Project\BpmnProject
     * @return null|\ProcessMaker\Project\BpmnProject
     */
    public function testLoad($bp)
    {
        $prjUid = $bp->getUid();
        $bp2 = BpmnProject::load($prjUid);

        $this->assertNotNull($bp2);
        $this->assertEquals($bp->getActivities(), $bp2->getActivities());
        $this->assertEquals($bp->getDiagram(), $bp2->getDiagram());
        $this->assertEquals($bp->getProcess(), $bp2->getProcess());

        return $bp2;
    }

    /**
     * @depends testLoad
     * @param $bp \ProcessMaker\Project\BpmnProject
     */
    public function testRemove($bp)
    {
        $prjUid = $bp->getUid();

        $bp->remove();

        $this->assertNull(BpmnProject::load($prjUid));
    }
}

