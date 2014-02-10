<?php
namespace Tests\ProcessMaker\Project\Adapter;

use \ProcessMaker\Project;

if (! class_exists("Propel")) {
    include_once __DIR__ . "/../../../bootstrap.php";
}

/**
 * Class WorkflowBpmnTest
 *
 * @package Tests\ProcessMaker\Project\Adapter
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class WorkflowBpmnTest extends \PHPUnit_Framework_TestCase
{
    protected static $uids = array();

    public static function tearDownAfterClass()
    {
        //cleaning DB
        foreach (self::$uids as $prjUid) {
            $wbpa = Project\Adapter\WorkflowBpmn::load($prjUid);
            $wbpa->remove();
        }
    }

    function testNew()
    {
        $data = array(
            "PRO_TITLE" => "Test Workflow/Bpmn Project #1",
            "PRO_DESCRIPTION" => "Description for - Test Project #1",
            "PRO_CREATE_USER" => "00000000000000000000000000000001"
        );

        $wbap = new Project\Adapter\WorkflowBpmn($data);

        try {
            $bp = Project\Bpmn::load($wbap->getUid());
        } catch (\Exception $e){die($e->getMessage());}

        try {
            $wp = Project\Workflow::load($wbap->getUid());
        } catch (\Exception $e){}

        self::$uids[] = $wbap->getUid();

        $this->assertNotNull($bp);
        $this->assertNotNull($wp);
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
            "PRO_TITLE" => "Test Workflow/Bpmn Project #2",
            "PRO_DESCRIPTION" => "Description for - Test Project #2",
            "PRO_CREATE_USER" => "00000000000000000000000000000001"
        );
        $wbap = new Project\Adapter\WorkflowBpmn();
        $wbap->create($data);

        try {
            $bp = Project\Bpmn::load($wbap->getUid());
        } catch (\Exception $e){}

        try {
            $wp = Project\Workflow::load($wbap->getUid());
        } catch (\Exception $e){}

        self::$uids[] = $wbap->getUid();

        $this->assertNotEmpty($bp);
        $this->assertNotEmpty($wp);
        $this->assertEquals($bp->getUid(), $wp->getUid());

        $project = $bp->getProject();
        $process = $wp->getProcess();

        $this->assertEquals($project["PRJ_NAME"], $process["PRO_TITLE"]);
        $this->assertEquals($project["PRJ_DESCRIPTION"], $process["PRO_DESCRIPTION"]);
        $this->assertEquals($project["PRJ_AUTHOR"], $process["PRO_CREATE_USER"]);
    }
}