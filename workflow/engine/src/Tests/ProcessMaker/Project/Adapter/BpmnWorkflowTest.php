<?php
namespace Tests\ProcessMaker\Project\Adapter;

use \ProcessMaker\Project;
use \ProcessMaker\Exception;


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
    protected static $uids = array();

    public static function tearDownAfterClass()
    {
        //return false;
        //cleaning DB
        foreach (self::$uids as $prjUid) {
            $bwap = Project\Adapter\BpmnWorkflow::load($prjUid);
            $bwap->remove();
        }
    }

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
        } catch (\Exception $e){
            $bp = null;
        }

        try {
            $wp = Project\Workflow::load($bwap->getUid());
        } catch (\Exception $e){
            $wp = null;
        }

        self::$uids[] = $bwap->getUid();

        $this->assertNotNull($bp);
        $this->assertNotNull($wp);
        $this->assertEquals($bp->getUid(), $wp->getUid());

        $project = $bp->getProject();
        $process = $wp->getProcess();

        $this->assertEquals($project["PRJ_NAME"], $process["PRO_TITLE"]);
        $this->assertEquals($project["PRJ_DESCRIPTION"], $process["PRO_DESCRIPTION"]);
        $this->assertEquals($project["PRJ_AUTHOR"], $process["PRO_CREATE_USER"]);

        return $bwap;
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
        } catch (\Exception $e){
            $bp = null;
        }

        try {
            $wp = Project\Workflow::load($bwap->getUid());
        } catch (\Exception $e){
            $wp = null;
        }

        $this->assertNotEmpty($bp);
        $this->assertNotEmpty($wp);
        $this->assertEquals($bp->getUid(), $wp->getUid());

        $project = $bp->getProject();
        $process = $wp->getProcess();

        $this->assertEquals($project["PRJ_NAME"], $process["PRO_TITLE"]);
        $this->assertEquals($project["PRJ_DESCRIPTION"], $process["PRO_DESCRIPTION"]);
        $this->assertEquals($project["PRJ_AUTHOR"], $process["PRO_CREATE_USER"]);

        return $bwap;
    }

    /**
     * @depends testCreate
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     */
    function testRemove(Project\Adapter\BpmnWorkflow $bwap)
    {
        $prjUid = $bwap->getUid();
        $bwap->remove();
        $bp = $wp = null;

        try {
            $bp = Project\Bpmn::load($prjUid);
        } catch (Exception\ProjectNotFound $e) {}

        try {
            $wp = Project\Workflow::load($prjUid);
        } catch (Exception\ProjectNotFound $e) {}

        $this->assertNull($bp);
        $this->assertNull($wp);
    }

    /*
     * Testing Project's Activity
     */

    /**
     * @depends testNew
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     * @return string
     */
    function testAddActivity($bwap)
    {
        // before add activity, we need to add a diagram and process to the project
        $bwap->addDiagram();
        $bwap->addProcess();

        // add the new activity
        $actUid = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #1",
            "BOU_X" => "50",
            "BOU_Y" => "50"
        ));

        $wp = Project\Workflow::load($bwap->getUid());

        $activity = $bwap->getActivity($actUid);
        $task = $wp->getTask($actUid);

        $this->assertEquals($activity["ACT_NAME"], $task["TAS_TITLE"]);
        $this->assertEquals($activity["BOU_X"], $task["TAS_POSX"]);
        $this->assertEquals($activity["BOU_Y"], $task["TAS_POSY"]);

        return $actUid;
    }

    /**
     * @depends testNew
     * @depends testAddActivity
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     * @param string $actUid
     */
    function testUpdateActivity($bwap, $actUid)
    {
        $updatedData = array(
            "ACT_NAME" => "Activity #1 - (Modified)",
            "BOU_X" => 122,
            "BOU_Y" => 250
        );

        $bwap->updateActivity($actUid, $updatedData);
        $activity = $bwap->getActivity($actUid);
        $wp = Project\Workflow::load($bwap->getUid());
        $task = $wp->getTask($actUid);

        $this->assertEquals($activity["ACT_NAME"], $task["TAS_TITLE"]);
        $this->assertEquals($activity["BOU_X"], $task["TAS_POSX"]);
        $this->assertEquals($activity["BOU_Y"], $task["TAS_POSY"]);
    }

    /**
     * @depends testNew
     * @depends testAddActivity
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     * @param string $actUid
     */
    function testRemoveActivity($bwap, $actUid)
    {
        $bwap->removeActivity($actUid);
        $activity = $bwap->getActivity($actUid);

        $this->assertNull($activity);
    }

    /*
     * Testing Project's Flows
     */

    /**
     * @depends testNew
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     * @return string
     */
    function testAddActivityToActivityFlow($bwap)
    {
        $actUid1 = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #1",
            "BOU_X" => 122,
            "BOU_Y" => 222
        ));
        $actUid2 = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #2",
            "BOU_X" => 322,
            "BOU_Y" => 422
        ));

        $flowData = array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $actUid1,
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnActivity',
            'FLO_ELEMENT_DEST' => $actUid2,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnActivity',
            'FLO_X1' => 326,
            'FLO_Y1' => 146,
            'FLO_X2' => 461,
            'FLO_Y2' => 146,
        );

        $flowUid = $bwap->addFlow($flowData);
        $bwap->mapBpmnFlowsToWorkflowRoutes();

        $route = \Route::findOneBy(array(
            \RoutePeer::TAS_UID => $actUid1,
            \RoutePeer::ROU_NEXT_TASK => $actUid2
        ));

        $this->assertNotNull($route);
        $this->assertTrue(is_string($flowUid));
        $this->assertEquals(32, strlen($flowUid));
        $this->assertEquals($route->getRouNextTask(), $actUid2);
        $this->assertEquals($route->getRouType(), "SEQUENTIAL");

        return array("flow_uid" => $flowUid, "activitiesUid" => array($actUid1, $actUid2));
    }


    /**
     * @depends testNew
     * @depends testAddActivityToActivityFlow
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     * @param array $input
     */
    function testRemoveActivityToActivityFlow($bwap, $input)
    {
        $bwap->removeFlow($input["flow_uid"]);
        $this->assertNull($bwap->getFlow($input["flow_uid"]));

        $route = \Route::findOneBy(array(
            \RoutePeer::TAS_UID => $input["activitiesUid"][0],
            \RoutePeer::ROU_NEXT_TASK => $input["activitiesUid"][1]
        ));

        $this->assertNull($route);

        // cleaning
        $bwap->removeActivity($input["activitiesUid"][0]);
        $bwap->removeActivity($input["activitiesUid"][1]);

        $this->assertCount(0, $bwap->getActivities());
    }

    /**
     * @depends testNew
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     */
    function testActivityToInclusiveGatewayToActivityFlowsSingle($bwap)
    {
        $actUid1 = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #1",
            "BOU_X" => 198,
            "BOU_Y" => 56
        ));
        $actUid2 = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #2",
            "BOU_X" => 198,
            "BOU_Y" => 250
        ));
        $gatUid = $bwap->addGateway(array(
            "GAT_NAME" => "Gateway #1",
            "GAT_TYPE" => "INCLUSIVE",
            "GAT_DIRECTION" => "DIVERGING",
            "BOU_X" => 256,
            "BOU_Y" => 163
        ));

        $flowUid1 = $bwap->addFlow(array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $actUid1,
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnActivity',
            'FLO_ELEMENT_DEST' => $gatUid,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnGateway',
            'FLO_X1' => 273,
            'FLO_Y1' => 273,
            'FLO_X2' => 163,
            'FLO_Y2' => 163,
        ));

        $flowUid2 = $bwap->addFlow(array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $gatUid,
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnGateway',
            'FLO_ELEMENT_DEST' => $actUid2,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnActivity',
            'FLO_X1' => 273,
            'FLO_Y1' => 273,
            'FLO_X2' => 249,
            'FLO_Y2' => 249,
        ));

        $bwap->mapBpmnFlowsToWorkflowRoutes();

        // cleaning
        $bwap->removeActivity($actUid1);
        $bwap->removeActivity($actUid2);
        $bwap->removeGateway($gatUid);

        $this->assertCount(0, $bwap->getActivities());
        $this->assertCount(0, $bwap->getGateways());
        $this->assertCount(0, $bwap->getFlows());

        $wp = Project\Workflow::load($bwap->getUid());

        $this->assertCount(0, $wp->getTasks());
        $this->assertCount(0, $wp->getRoutes());
    }

    /**
     * @depends testNew
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     */
    function testActivityToInclusiveGatewayToActivityFlowsMultiple($bwap)
    {
        /*$actUid1 = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #1",
            "BOU_X" => 198,
            "BOU_Y" => 56
        ));
        $actUid2 = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #2",
            "BOU_X" => 198,
            "BOU_Y" => 250
        ));
        $gatUid = $bwap->addGateway(array(
            "GAT_NAME" => "Gateway #1",
            "GAT_TYPE" => "INCLUSIVE",
            "GAT_DIRECTION" => "DIVERGING",
            "BOU_X" => 256,
            "BOU_Y" => 163
        ));

        $flowUid1 = $bwap->addFlow(array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $actUid1,
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnActivity',
            'FLO_ELEMENT_DEST' => $gatUid,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnGateway',
            'FLO_X1' => 273,
            'FLO_Y1' => 273,
            'FLO_X2' => 163,
            'FLO_Y2' => 163,
        ));

        $flowUid1 = $bwap->addFlow(array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $gatUid,
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnGateway',
            'FLO_ELEMENT_DEST' => $actUid2,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnActivity',
            'FLO_X1' => 273,
            'FLO_Y1' => 273,
            'FLO_X2' => 249,
            'FLO_Y2' => 249,
        ));

        $bwap->mapBpmnFlowsToWorkflowRoutes();*/
    }
}


