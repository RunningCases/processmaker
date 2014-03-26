<?php
namespace ProcessMaker\Project;

use \BpmnProject as Project;
use \BpmnProcess as Process;
use \BpmnDiagram as Diagram;
use \BpmnLaneset as Laneset;
use \BpmnLane as Lane;
use \BpmnActivity as Activity;
use \BpmnBound as Bound;
use \BpmnEvent as Event;
use \BpmnGateway as Gateway;
use \BpmnFlow as Flow;
use \BpmnArtifact as Artifact;

use \BpmnProjectPeer as ProjectPeer;
use \BpmnProcessPeer as ProcessPeer;
use \BpmnDiagramPeer as DiagramPeer;
use \BpmnLanesetPeer as LanesetPeer;
use \BpmnLanePeer as LanePeer;
use \BpmnActivityPeer as ActivityPeer;
use \BpmnBoundPeer as BoundPeer;
use \BpmnEventPeer as EventPeer;
use \BpmnGatewayPeer as GatewayPeer;
use \BpmnFlowPeer as FlowPeer;
use \BpmnArtifactPeer as ArtifactPeer;

use \BasePeer;

use ProcessMaker\Util\Common;
use ProcessMaker\Exception;

/**
 * Class Bpmn
 *
 * @package ProcessMaker\Project
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class Bpmn extends Handler
{
    /**
     * @var \BpmnProject
     */
    protected $project;

    protected $prjUid;

    /**
     * @var \BpmnProcess
     */
    protected $process;

    /**
     * @var \BpmnDiagram
     */
    protected $diagram;

    protected static $excludeFields = array(
        "activity" => array(
            "PRJ_UID", "PRO_UID", "BOU_ELEMENT", "BOU_ELEMENT_TYPE", "BOU_REL_POSITION",
            "BOU_SIZE_IDENTICAL", "DIA_UID", "BOU_UID", "ELEMENT_UID"
        ),
        "event" => array(
            "PRJ_UID", "PRO_UID", "BOU_ELEMENT", "BOU_ELEMENT_TYPE", "BOU_REL_POSITION",
            "BOU_SIZE_IDENTICAL", "DIA_UID", "BOU_UID", "ELEMENT_UID", "EVN_ATTACHED_TO", "EVN_CONDITION"
        ),
        "gateway" => array("BOU_ELEMENT", "BOU_ELEMENT_TYPE", "BOU_REL_POSITION", "BOU_SIZE_IDENTICAL", "BOU_UID",
            "DIA_UID", "ELEMENT_UID", "PRJ_UID", "PRO_UID"
        ),
        "flow" => array("PRJ_UID", "DIA_UID", "FLO_ELEMENT_DEST_PORT", "FLO_ELEMENT_ORIGIN_PORT")
    );


    public function __construct($data = null)
    {
        if (! is_null($data)) {
            $this->create($data);
        }
    }

    public static function load($prjUid)
    {
        $me = new self();
        $project = ProjectPeer::retrieveByPK($prjUid);

        if (! is_object($project)) {
            throw new Exception\ProjectNotFound($me, $prjUid);
        }

        $me->project = $project;
        $me->prjUid = $me->project->getPrjUid();

        return $me;
    }

    /**
     * @param array| $data array attributes to create and initialize a BpmnProject
     */
    public function create($data)
    {
        // setting defaults
        $data['PRJ_UID'] = array_key_exists('PRJ_UID', $data) ? $data['PRJ_UID'] : Common::generateUID();

        self::log("Create Project with data: ", $data);
        $this->project = new Project();
        $this->project->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $this->project->setPrjCreateDate(date("Y-m-d H:i:s"));
        $this->project->save();

        $this->prjUid = $this->project->getPrjUid();
        self::log("Create Project Success!");
    }

    public function update()
    {

    }

    public function remove()
    {
        /*
         * 1. Remove Diagram related objects
         * 2. Remove Project related objects
         */

        self::log("Remove Project With Uid: {$this->prjUid}");
        foreach ($this->getActivities() as $activity) {
            $this->removeActivity($activity["ACT_UID"]);
        }
        foreach ($this->getGateways() as $gateway) {
            $this->removeGateway($gateway["GAT_UID"]);
        }
        foreach ($this->getEvents() as $event) {
            $this->removeEvent($event["EVN_UID"]);
        }
        foreach ($this->getFlows() as $flow) {
            $this->removeFlow($flow["FLO_UID"]);
        }

        if ($process = $this->getProcess("object")) {
            $process->delete();
        }
        if ($diagram = $this->getDiagram("object")) {
            $diagram->delete();
        }
        if ($project = $this->getProject("object")) {
            $project->delete();
        }
        self::log("Remove Project Success!");
    }

    public static function removeIfExists($prjUid)
    {
        $project = ProjectPeer::retrieveByPK($prjUid);

        if ($project) {
            $me = new self();
            $me->prjUid = $project->getPrjUid();
            $me->project = $project;
            $me->remove();
        }
    }

    public static function getList($start = null, $limit = null, $filter = "", $changeCaseTo = CASE_UPPER)
    {
        return Project::getAll($start, $limit, $filter, $changeCaseTo);
    }

    public function getUid()
    {
        if (empty($this->project)) {
            throw new \RuntimeException("Error: There is not an initialized project.");
        }

        return $this->prjUid;
    }

    public function getProject($retType = "array")
    {
        if (empty($this->project)) {
            throw new \RuntimeException("Error: There is not an initialized project.");
        }

        return $retType == "array" ? $this->project->toArray() : $this->project;
    }

    /*
     * Projects elements handlers
     */

    public function addDiagram($data = array())
    {
        if (empty($this->project)) {
            throw new \Exception("Error: There is not an initialized project.");
        }

        // setting defaults
        $data['DIA_UID'] = array_key_exists('DIA_UID', $data) ? $data['DIA_UID'] : Common::generateUID();
        $data['DIA_NAME'] = array_key_exists('DIA_NAME', $data) ? $data['DIA_NAME'] : $this->project->getPrjName();

        $this->diagram = new Diagram();
        $this->diagram->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $this->diagram->setPrjUid($this->project->getPrjUid());
        $this->diagram->save();
    }

    public function getDiagram($retType = "array")
    {
        if (empty($this->diagram)) {
            $diagrams = Diagram::findAllByProUid($this->getUid());

            if (! empty($diagrams)) {
                //NOTICE for ProcessMaker we're just handling a "one to one" relationship between project and process
                $this->diagram = $diagrams[0];
            }
        }

        return ($retType == "array" && is_object($this->diagram)) ? $this->diagram->toArray() : $this->diagram;
    }

    public function addProcess($data = array())
    {
        if (empty($this->diagram)) {
            throw new \Exception("Error: There is not an initialized diagram.");
        }

        // setting defaults
        $data['PRO_UID'] = array_key_exists('PRO_UID', $data) ? $data['PRO_UID'] : Common::generateUID();;
        $data['PRO_NAME'] = array_key_exists('PRO_NAME', $data) ? $data['PRO_NAME'] : $this->diagram->getDiaName();

        $this->process = new Process();
        $this->process->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $this->process->setPrjUid($this->project->getPrjUid());
        $this->process->setDiaUid($this->getDiagram("object")->getDiaUid());
        $this->process->save();
    }

    public function getProcess($retType = "array")
    {
        if (empty($this->process)) {
            $processes = Process::findAllByProUid($this->getUid());

            if (! empty($processes)) {
                //NOTICE for ProcessMaker we're just handling a "one to one" relationship between project and process
                $this->process = $processes[0];
            }
        }

        return $retType == "array" ? $this->process->toArray() : $this->process;
    }

    public function addActivity($data)
    {
        if (! ($process = $this->getProcess("object"))) {
            throw new \Exception(sprintf("Error: There is not an initialized diagram for Project with prj_uid: %s.", $this->getUid()));
        }

        // setting defaults
        $data['ACT_UID'] = array_key_exists('ACT_UID', $data) ? $data['ACT_UID'] : Common::generateUID();;

        try {
            self::log("Add Activity with data: ", $data);

            $activity = new Activity();
            $activity->fromArray($data);
            $activity->setPrjUid($this->getUid());
            $activity->setProUid($process->getProUid());
            $activity->save();

            self::log("Add Activity Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

        return $activity->getActUid();
    }

    public function getActivity($actUid, $retType = 'array')
    {
        $activity = ActivityPeer::retrieveByPK($actUid);

        if ($retType != "object" && ! empty($activity)) {
            $activity = $activity->toArray();
            $activity = self::filterArrayKeys($activity, self::$excludeFields["activity"]);
        }

        return $activity;
    }

    public function getActivities($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        $filter = $changeCaseTo != CASE_UPPER ? array_map("strtolower", self::$excludeFields["activity"]) : self::$excludeFields["activity"];

        return self::filterCollectionArrayKeys(
            Activity::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo),
            $filter
        );
    }

    public function updateActivity($actUid, $data)
    {
        try {
            self::log("Update Activity: $actUid, with data: ", $data);

            $activity = ActivityPeer::retrieveByPk($actUid);
            $activity->fromArray($data);
            $activity->save();

            self::log("Update Activity Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function removeActivity($actUid)
    {
        try {
            self::log("Remove Activity: $actUid");

            $activity = ActivityPeer::retrieveByPK($actUid);
            $activity->delete();
            //TODO if the activity was removed, the related flows to that activity must be removed

            self::log("Remove Activity Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function activityExists($actUid)
    {
        return \BpmnActivity::exists($actUid);
    }

    public function addEvent($data)
    {
        // setting defaults
        $data['EVN_UID'] = array_key_exists('EVN_UID', $data) ? $data['EVN_UID'] : Common::generateUID();

        try {
            self::log("Add Event with data: ", $data);

            $event = new Event();
            $event->fromArray($data);
            $event->setPrjUid($this->project->getPrjUid());
            $event->setProUid($this->getProcess("object")->getProUid());
            $event->save();

            self::log("Add Event Success!");

            return $event->getEvnUid();
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function getEvent($evnUid, $retType = 'array')
    {
        $event = EventPeer::retrieveByPK($evnUid);

        if ($retType != "object" && ! empty($event)) {
            $event = $event->toArray();
            $event = self::filterArrayKeys($event, self::$excludeFields["event"]);
        }

        return $event;
    }

    public function getEvents($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        //return Event::getAll($this->project->getPrjUid(), null, null, '', $changeCaseTo);

        $filter = $changeCaseTo != CASE_UPPER ? array_map("strtolower", self::$excludeFields["event"]) : self::$excludeFields["event"];

        return self::filterCollectionArrayKeys(
            Event::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo),
            $filter
        );
    }

    public function updateEvent($evnUid, $data)
    {
        /*if (array_key_exists("EVN_CANCEL_ACTIVITY", $data)) {
            $data["EVN_CANCEL_ACTIVITY"] = $data["EVN_CANCEL_ACTIVITY"] ? 1 : 0;
        }

        if (array_key_exists("EVN_WAIT_FOR_COMPLETION", $data)) {
            $data["EVN_WAIT_FOR_COMPLETION"] = $data["EVN_WAIT_FOR_COMPLETION"] ? 1 : 0;
        }*/

        try {
            self::log("Update Event: $evnUid", "With data: ", $data);

            $event = EventPeer::retrieveByPk($evnUid);
            $event->fromArray($data);
            $event->save();

            self::log("Update Event Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function removeEvent($evnUid)
    {
        try {
            self::log("Remove Event: $evnUid");

            $event = EventPeer::retrieveByPK($evnUid);
            $event->delete();

            self::log("Remove Event Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function addGateway($data)
    {
        // setting defaults
        $data['GAT_UID'] = array_key_exists('GAT_UID', $data) ? $data['GAT_UID'] : Common::generateUID();

        try {
            self::log("Add Gateway with data: ", $data);
            $gateway = new Gateway();
            $gateway->fromArray($data);
            $gateway->setPrjUid($this->getUid());
            $gateway->setProUid($this->getProcess("object")->getProUid());
            $gateway->save();
            self::log("Add Gateway Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

        return $gateway->getGatUid();
    }

    public function updateGateway($gatUid, $data)
    {
        try {
            self::log("Update Gateway: $gatUid", "With data: ", $data);

            $gateway = GatewayPeer::retrieveByPk($gatUid);

            $gateway->fromArray($data);
            $gateway->save();

            self::log("Update Gateway Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function getGateway($gatUid, $retType = 'array')
    {
        $gateway = GatewayPeer::retrieveByPK($gatUid);

        if ($retType != "object" && ! empty($gateway)) {
            $gateway = $gateway->toArray();
            $gateway = self::filterArrayKeys($gateway, self::$excludeFields["gateway"]);
        }

        return $gateway;
    }

    public function getGateways($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        //return  Gateway::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo);
        $filter = $changeCaseTo != CASE_UPPER ? array_map("strtolower", self::$excludeFields["gateway"]) : self::$excludeFields["gateway"];

        return self::filterCollectionArrayKeys(
            Gateway::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo),
            $filter
        );
    }

    public function removeGateway($gatUid)
    {
        try {
            self::log("Remove Gateway: $gatUid");

            $gateway = GatewayPeer::retrieveByPK($gatUid);
            $gateway->delete();

            // remove related object (flows)
            Flow::removeAllRelated($gatUid);

            self::log("Remove Gateway Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function addFlow($data)
    {
        self::log("Add Flow with data: ", $data);

        // setting defaults
        $data['FLO_UID'] = array_key_exists('FLO_UID', $data) ? $data['FLO_UID'] : Common::generateUID();
        if (array_key_exists('FLO_STATE', $data)) {
            $data['FLO_STATE'] = is_array($data['FLO_STATE']) ? json_encode($data['FLO_STATE']) : $data['FLO_STATE'];
        }

        try {
            switch ($data["FLO_ELEMENT_ORIGIN_TYPE"]) {
                case "bpmnActivity": $class = "BpmnActivity"; break;
                case "bpmnGateway": $class = "BpmnGateway"; break;
                case "bpmnEvent": $class = "BpmnEvent"; break;
                default:
                    throw new \RuntimeException(sprintf("Invalid Object type, accepted types: [%s|%s|%s], given %s.",
                        "BpmnActivity", "BpmnBpmnGateway", "BpmnEvent", $data["FLO_ELEMENT_ORIGIN_TYPE"]
                    ));
            }

            // Validate origin object exists
            if (! $class::exists($data["FLO_ELEMENT_ORIGIN"])) {
                throw new \RuntimeException(sprintf("Reference not found, the %s with UID: %s, does not exist!",
                    ucfirst($data["FLO_ELEMENT_ORIGIN_TYPE"]), $data["FLO_ELEMENT_ORIGIN"]
                ));
            }

            switch ($data["FLO_ELEMENT_DEST_TYPE"]) {
                case "bpmnActivity": $class = "BpmnActivity"; break;
                case "bpmnGateway": $class = "BpmnGateway"; break;
                case "bpmnEvent": $class = "BpmnEvent"; break;
                default:
                    throw new \RuntimeException(sprintf("Invalid Object type, accepted types: [%s|%s|%s], given %s.",
                        "BpmnActivity", "BpmnBpmnGateway", "BpmnEvent", $data["FLO_ELEMENT_DEST_TYPE"]
                    ));
            }

            // Validate origin object exists
            if (! $class::exists($data["FLO_ELEMENT_DEST"])) {
                throw new \RuntimeException(sprintf("Reference not found, the %s with UID: %s, does not exist!",
                    ucfirst($data["FLO_ELEMENT_DEST_TYPE"]), $data["FLO_ELEMENT_DEST"]
                ));
            }

            $flow = new Flow();
            $flow->fromArray($data, BasePeer::TYPE_FIELDNAME);
            $flow->setPrjUid($this->getUid());
            $flow->setDiaUid($this->getDiagram("object")->getDiaUid());
            $flow->save();
            self::log("Add Flow Success!");

            return $flow->getFloUid();
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function updateFlow($floUid, $data)
    {
        self::log("Update Flow: $floUid", "With data: ", $data);

        // setting defaults
        if (array_key_exists('FLO_STATE', $data)) {
            $data['FLO_STATE'] = is_array($data['FLO_STATE']) ? json_encode($data['FLO_STATE']) : $data['FLO_STATE'];
        }
        try {
            $flow = FlowPeer::retrieveByPk($floUid);
            $flow->fromArray($data);
            $flow->save();

            self::log("Update Flow Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function getFlow($floUid, $retType = 'array')
    {
        $flow = FlowPeer::retrieveByPK($floUid);

        if ($retType != "object" && ! empty($flow)) {
            $flow = $flow->toArray();
            $flow = self::filterArrayKeys($flow, self::$excludeFields["flow"]);
        }

        return $flow;
    }

    public function getFlows($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        $filter = $changeCaseTo != CASE_UPPER ? array_map("strtolower", self::$excludeFields["flow"]) : self::$excludeFields["flow"];

        return self::filterCollectionArrayKeys(
            Flow::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo),
            $filter
        );
    }

    public function removeFlow($floUid)
    {
        try {
            self::log("Remove Flow: $floUid");

            $flow = FlowPeer::retrieveByPK($floUid);
            $flow->delete();

            self::log("Remove Flow Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function flowExists($floUid)
    {
        return \BpmnFlow::exists($floUid);
    }

    public function addArtifact($data)
    {
        // TODO: Implement update() method.
    }

    public function getArtifact($artUid)
    {
        // TODO: Implement update() method.
    }

    public function getArtifacts()
    {
        // TODO: Implement update() method.
        return array();
    }

    public function addLane($data)
    {
        // TODO: Implement update() method.
    }

    public function getLane($lanUid)
    {
        // TODO: Implement update() method.
    }

    public function getLanes()
    {
        // TODO: Implement update() method.
        return array();
    }

    public function addLaneset($data)
    {
        // TODO: Implement update() method.
    }

    public function getLaneset($lnsUid)
    {
        // TODO: Implement update() method.
    }

    public function getLanesets()
    {
        // TODO: Implement update() method.
        return array();
    }


    public function isModified($element, $uid, $newData)
    {
        $data = array();

        switch ($element) {
            case "activity": $data = $this->getActivity($uid); break;
            case "gateway":  $data = $this->getGateway($uid); break;
            case "event":    $data = $this->getEvent($uid); break;
            case "flow":     $data = $this->getFlow($uid); break;
        }
        //self::log("saved data: ", $data, "new data: ", $newData);
        //self::log("checksum saved data: ", self::getChecksum($data), "checksum new data: ", self::getChecksum($newData));
        return (self::getChecksum($data) !== self::getChecksum($newData));
    }
}