<?php
namespace Tests\ProcessMaker\Project\Adapter;

use \ProcessMaker\Project;

if (! class_exists("Propel")) {
    include_once __DIR__ . "/../../../bootstrap.php";
}

/**
 * Class BpmnWorkflowTest
 *
 * @package Tests\ProcessMaker\Project\Adapter
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class BpmnWorkflowTest extends \PHPUnit_Framework_TestCase
{
    function testNew()
    {
        $data = array(
            "PRJ_NAME" => "Test Bpmn/Workflow Project #1",
            "PRJ_DESCRIPTION" => "Description for - Test BPMN Project #1",
            "PRJ_AUTHOR" => "00000000000000000000000000000001"
        );

        $bwap = new Project\Adapter\BpmnWorkflow($data);

        try {
            $bp = Project\Bpmn::load($bwap->getUid());
        } catch (\Exception $e){}

        try {
            $wp = Project\Workflow::load($bwap->getUid());
        } catch (\Exception $e){}

        $this->assertNotEmpty($bp);
        $this->assertNotEmpty($wp);
        $this->assertEquals($bp->getUid(), $wp->getUid());

        $project = $bp->getProject();
        $process = $wp->getProcess();

        $this->assertEquals($project["PRJ_NAME"], $process["PRO_TITLE"]);
        $this->assertEquals($project["PRJ_DESCRIPTION"], $process["PRO_DESCRIPTION"]);
        $this->assertEquals($project["PRJ_AUTHOR"], $process["PRO_CREATE_USER"]);
    }

    function testCreate()
    {
        $data = array(
            "PRJ_NAME" => "Test Bpmn/Workflow Project #2",
            "PRJ_DESCRIPTION" => "Description for - Test BPMN Project #2",
            "PRJ_AUTHOR" => "00000000000000000000000000000001"
        );

        $bwap = new Project\Adapter\BpmnWorkflow();
        $bwap->create($data);

        try {
            $bp = Project\Bpmn::load($bwap->getUid());
        } catch (\Exception $e){}

        try {
            $wp = Project\Workflow::load($bwap->getUid());
        } catch (\Exception $e){}

        $this->assertNotEmpty($bp);
        $this->assertNotEmpty($wp);
        $this->assertEquals($bp->getUid(), $wp->getUid());

        $project = $bp->getProject();
        $process = $wp->getProcess();

        $this->assertEquals($project["PRJ_NAME"], $process["PRO_TITLE"]);
        $this->assertEquals($project["PRJ_DESCRIPTION"], $process["PRO_DESCRIPTION"]);
        $this->assertEquals($project["PRJ_AUTHOR"], $process["PRO_CREATE_USER"]);

        $bwap->addDiagram();
        $bwap->addProcess();

        // Save to DB
        $bwap->addActivity(array(
            "ACT_NAME" => "Activity #1",
            "BOU_X" => "50",
            "BOU_Y" => "50"
        ));

        $bwap->addActivity(array(
            "ACT_NAME" => "Activity #2",
            "BOU_X" => "250",
            "BOU_Y" => "250"
        ));
    }
}