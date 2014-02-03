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
        $bpLoaded = BpmnProject::load($bp->getUid());
        $diagramData = $bpLoaded->getDiagram();

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
        $bpLoaded = BpmnProject::load($bp->getUid());
        $processData = $bpLoaded->getProcess();


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

        $processData = $bp->getProcess();


        // Save to DB
        $bp->addActivity($data);

        // Load from DB
        $bpLoaded = BpmnProject::load($bp->getUid());
        $activities = $bpLoaded->getActivities();

        $this->assertCount(1, $activities);

        $activityData = $activities[0];

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $activityData[$key]);
        }

        $this->assertEquals($bpLoaded->getUid(), $activityData["PRJ_UID"]);
        $this->assertEquals($processData["PRO_UID"], $activityData["PRO_UID"]);
    }
}

