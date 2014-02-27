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
        return false;
        //cleaning DB
        foreach (self::$uids as $prjUid) {
            $bwap = Project\Adapter\BpmnWorkflow::load($prjUid);
            $bwap->remove();
        }
    }

    function testNew()
    {
        $data = array(
            "PRJ_NAME" => "Test Bpmn/Workflow Project #1.". rand(1, 100),
            "PRJ_DESCRIPTION" => "Description for - Test BPMN Project #1." . rand(1, 100),
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

        $bwap->addFlow(array(
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

        $bwap->addFlow(array(
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

        $this->assertCount(2, $bwap->getActivities());
        $this->assertCount(1, $bwap->getGateways());
        $this->assertCount(2, $bwap->getFlows());

        $flows1 = \BpmnFlow::findAllBy(\BpmnFlowPeer::FLO_ELEMENT_DEST, $gatUid);
        $flows2 = \BpmnFlow::findAllBy(\BpmnFlowPeer::FLO_ELEMENT_ORIGIN, $gatUid);

        $this->assertCount(1, $flows1);
        $this->assertCount(1, $flows2);
        $this->assertEquals($flows1[0]->getFloElementOrigin(), $actUid1);
        $this->assertEquals($flows2[0]->getFloElementDest(), $actUid2);

        // cleaning
        $this->resetProject($bwap);
    }

    /**
     * @depends testNew
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     */
    function testActivityToInclusiveGatewayToActivityFlowsMultiple($bwap)
    {
        $actUid1 = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #1",
            "BOU_X" => 311,
            "BOU_Y" => 26
        ));
        $actUid2 = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #2",
            "BOU_X" => 99,
            "BOU_Y" => 200
        ));
        $actUid3 = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #3",
            "BOU_X" => 310,
            "BOU_Y" => 200
        ));
        $actUid4 = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #4",
            "BOU_X" => 542,
            "BOU_Y" => 200
        ));
        $gatUid = $bwap->addGateway(array(
            "GAT_NAME" => "Gateway #1",
            "GAT_TYPE" => "INCLUSIVE",
            "GAT_DIRECTION" => "DIVERGING",
            "BOU_X" => 369,
            "BOU_Y" => 123
        ));

        $bwap->addFlow(array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $actUid1,
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnActivity',
            'FLO_ELEMENT_DEST' => $gatUid,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnGateway',
            'FLO_X1' => 386,
            'FLO_Y1' => 174,
            'FLO_X2' => 206,
            'FLO_Y2' => 206,
        ));
        $bwap->addFlow(array(
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
        $bwap->addFlow(array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $gatUid,
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnGateway',
            'FLO_ELEMENT_DEST' => $actUid3,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnActivity',
            'FLO_X1' => 386,
            'FLO_Y1' => 174,
            'FLO_X2' => 206,
            'FLO_Y2' => 206,
        ));
        $bwap->addFlow(array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $gatUid,
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnGateway',
            'FLO_ELEMENT_DEST' => $actUid4,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnActivity',
            'FLO_X1' => 386,
            'FLO_Y1' => 617,
            'FLO_X2' => 207,
            'FLO_Y2' => 207,
        ));

        $bwap->mapBpmnFlowsToWorkflowRoutes();

        $this->assertCount(4, $bwap->getActivities());
        $this->assertCount(1, $bwap->getGateways());
        $this->assertCount(4, $bwap->getFlows());
        $this->assertCount(1, \BpmnFlow::findAllBy(\BpmnFlowPeer::FLO_ELEMENT_DEST, $gatUid));
        $this->assertCount(3, \BpmnFlow::findAllBy(\BpmnFlowPeer::FLO_ELEMENT_ORIGIN, $gatUid));

        $wp = Project\Workflow::load($bwap->getUid());

        $this->assertCount(4, $wp->getTasks());
        $this->assertCount(3, $wp->getRoutes());
        $this->assertCount(3, \Route::findAllBy(\RoutePeer::TAS_UID, $actUid1));
        $this->assertCount(1, \Route::findAllBy(\RoutePeer::ROU_NEXT_TASK, $actUid2));
        $this->assertCount(1, \Route::findAllBy(\RoutePeer::ROU_NEXT_TASK, $actUid3));
        $this->assertCount(1, \Route::findAllBy(\RoutePeer::ROU_NEXT_TASK, $actUid4));

        return array($actUid2, $actUid3, $actUid4);
    }

    /**
     * @depends testNew
     * @depends testActivityToInclusiveGatewayToActivityFlowsMultiple
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     * @param array $activitiesUid
     */
    function testActivityToInclusiveGatewayToActivityFlowsMultipleJoin($bwap, $activitiesUid)
    {
        $gatUid = $bwap->addGateway(array(
            "GAT_NAME" => "Gateway #2",
            "GAT_TYPE" => "INCLUSIVE",
            "GAT_DIRECTION" => "CONVERGING",
            "BOU_X" => 369,
            "BOU_Y" => 338
        ));
        $actUid5 = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #5",
            "BOU_X" => 312,
            "BOU_Y" => 464
        ));
        $bwap->addFlow(array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $activitiesUid[0],
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnActivity',
            'FLO_ELEMENT_DEST' => $gatUid,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnGateway',
            'FLO_X1' => 174,
            'FLO_Y1' => 365,
            'FLO_X2' => 355,
            'FLO_Y2' => 355,
        ));
        $bwap->addFlow(array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $activitiesUid[1],
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnActivity',
            'FLO_ELEMENT_DEST' => $gatUid,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnGateway',
            'FLO_X1' => 385,
            'FLO_Y1' => 382,
            'FLO_X2' => 338,
            'FLO_Y2' => 338,
        ));
        $bwap->addFlow(array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $activitiesUid[2],
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnActivity',
            'FLO_ELEMENT_DEST' => $gatUid,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnGateway',
            'FLO_X1' => 617,
            'FLO_Y1' => 398,
            'FLO_X2' => 355,
            'FLO_Y2' => 355,
        ));

        $bwap->addFlow(array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $gatUid,
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnGateway',
            'FLO_ELEMENT_DEST' => $actUid5,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnActivity',
            'FLO_X1' => 382,
            'FLO_Y1' => 387,
            'FLO_X2' => 463,
            'FLO_Y2' => 463,
        ));

        $bwap->mapBpmnFlowsToWorkflowRoutes();

        $this->assertCount(8, $bwap->getFlows());
        $this->assertCount(5, $bwap->getActivities());
        $this->assertCount(2, $bwap->getGateways());
        $this->assertCount(3, \BpmnFlow::findAllBy(\BpmnFlowPeer::FLO_ELEMENT_DEST, $gatUid));
        $this->assertCount(1, \BpmnFlow::findAllBy(\BpmnFlowPeer::FLO_ELEMENT_ORIGIN, $gatUid));

        $wp = Project\Workflow::load($bwap->getUid());

        $this->assertCount(5, $wp->getTasks());
        $this->assertCount(6, $wp->getRoutes());

        $this->assertCount(1, \Route::findAllBy(\RoutePeer::TAS_UID, $activitiesUid[0]));
        $this->assertCount(1, \Route::findAllBy(\RoutePeer::TAS_UID, $activitiesUid[1]));
        $this->assertCount(1, \Route::findAllBy(\RoutePeer::TAS_UID, $activitiesUid[2]));
        $this->assertCount(3, \Route::findAllBy(\RoutePeer::ROU_NEXT_TASK, $actUid5));

        // cleaning
        $this->resetProject($bwap);
    }

    /**
     * @depends testNew
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     * @return string
     */
    function testSetStartEvent($bwap)
    {
        $actUid = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #1",
            "BOU_X" => 312,
            "BOU_Y" => 464
        ));
        $evnUid = $bwap->addEvent(array(
            "EVN_NAME" => "Event #1",
            "EVN_TYPE" => "START",
            "BOU_X" => 369,
            "BOU_Y" => 338,
            "EVN_MARKER" => "MESSAGE",
            "EVN_MESSAGE" => "LEAD"
        ));
        $floUid = $bwap->addFlow(array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $evnUid,
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnEvent',
            'FLO_ELEMENT_DEST' => $actUid,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnActivity',
            'FLO_X1' => 174,
            'FLO_Y1' => 365,
            'FLO_X2' => 355,
            'FLO_Y2' => 355,
        ));

        $this->assertCount(1, $bwap->getActivities());
        $this->assertCount(1, $bwap->getEvents());
        $this->assertCount(1, $bwap->getFlows());

        $wp = Project\Workflow::load($bwap->getUid());
        $task = $wp->getTask($actUid);

        $this->assertCount(1, $wp->getTasks());
        $this->assertCount(0, $wp->getRoutes());
        $this->assertNotNull($task);

        $this->assertEquals($task["TAS_START"], "TRUE");

        return $floUid;
    }

    /**
     * @depends testNew
     * @depends testSetStartEvent
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     * @param string $floUid
     */
    function testUnsetStartEvent($bwap, $floUid)
    {
        $bwap->removeFlow($floUid);

        $this->assertCount(1, $bwap->getActivities());
        $this->assertCount(1, $bwap->getEvents());
        $this->assertCount(0, $bwap->getFlows());

        $wp = Project\Workflow::load($bwap->getUid());

        $tasks = $wp->getTasks();
        $this->assertCount(1, $tasks);
        $this->assertCount(0, $wp->getRoutes());
        $this->assertEquals($tasks[0]["TAS_START"], "FALSE");

        // cleaning
        $this->resetProject($bwap);
    }

    /**
     * @depends testNew
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     */
    function testSetEndEvent($bwap)
    {
        $actUid = $bwap->addActivity(array(
            "ACT_NAME" => "Activity #1",
            "BOU_X" => 312,
            "BOU_Y" => 464
        ));
        $evnUid = $bwap->addEvent(array(
            "EVN_NAME" => "Event #1",
            "EVN_TYPE" => "END",
            "BOU_X" => 369,
            "BOU_Y" => 338,
            "EVN_MARKER" => "MESSAGE",
            "EVN_MESSAGE" => "LEAD"
        ));
        $floUid = $bwap->addFlow(array(
            'FLO_TYPE' => 'SEQUENCE',
            'FLO_ELEMENT_ORIGIN' => $actUid,
            'FLO_ELEMENT_ORIGIN_TYPE' => 'bpmnActivity',
            'FLO_ELEMENT_DEST' => $evnUid,
            'FLO_ELEMENT_DEST_TYPE' => 'bpmnEvent',
            'FLO_X1' => 174,
            'FLO_Y1' => 365,
            'FLO_X2' => 355,
            'FLO_Y2' => 355,
        ));

        $this->assertCount(1, $bwap->getActivities());
        $this->assertCount(1, $bwap->getEvents());
        $this->assertCount(1, $bwap->getFlows());

        $wp = Project\Workflow::load($bwap->getUid());
        $task = $wp->getTask($actUid);

        $this->assertCount(1, $wp->getTasks());
        $this->assertCount(1, $wp->getRoutes());
        $this->assertNotNull($task);

        $routes = \Route::findAllBy(\RoutePeer::TAS_UID, $task["TAS_UID"]);

        $this->assertCount(1, $routes);
        $this->assertEquals($routes[0]->getRouNextTask(), "-1");

        return $floUid;
    }

    /**
     * @depends testNew
     * @depends testSetEndEvent
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     * @param $floUid
     */
    function testUnsetEndEvent($bwap, $floUid)
    {
        $bwap->removeFlow($floUid);

        $this->assertCount(1, $bwap->getActivities());
        $this->assertCount(1, $bwap->getEvents());
        $this->assertCount(0, $bwap->getFlows());

        $wp = Project\Workflow::load($bwap->getUid());

        $this->assertCount(1, $wp->getTasks());
        $this->assertCount(0, $wp->getRoutes());

        // cleaning
        $this->resetProject($bwap);
    }

    /**
     * @param \ProcessMaker\Project\Adapter\BpmnWorkflow $bwap
     */
    protected function resetProject(\ProcessMaker\Project\Adapter\BpmnWorkflow $bwap)
    {
        // cleaning
        $activities = $bwap->getActivities();
        foreach ($activities as $activity) {
            $bwap->removeActivity($activity["ACT_UID"]);
        }
        $events = $bwap->getEvents();
        foreach ($events as $event) {
            $bwap->removeEvent($event["EVN_UID"]);
        }
        $gateways = $bwap->getGateways();
        foreach ($gateways as $gateway) {
            $bwap->removeGateway($gateway["GAT_UID"]);
        }
        $flows = $bwap->getFlows();
        foreach ($flows as $flow) {
            $bwap->removeFlow($flow["FLO_UID"]);
        }

        // verifying that project is cleaned
        $this->assertCount(0, $bwap->getActivities());
        $this->assertCount(0, $bwap->getEvents());
        $this->assertCount(0, $bwap->getGateways());
        $this->assertCount(0, $bwap->getFlows());

        $wp = Project\Workflow::load($bwap->getUid());

        $this->assertCount(0, $wp->getTasks());
        $this->assertCount(0, $wp->getRoutes());
    }
}


