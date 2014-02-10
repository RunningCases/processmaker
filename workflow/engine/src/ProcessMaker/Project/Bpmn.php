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

use ProcessMaker\Util\Hash;
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
        $data['PRJ_UID'] = array_key_exists('PRJ_UID', $data) ? $data['PRJ_UID'] : Hash::generateUID();

        $this->project = new Project();
        $this->project->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $this->project->setPrjCreateDate(date("Y-m-d H:i:s"));
        $this->project->save();

        $this->prjUid = $this->project->getPrjUid();
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

        $activities = $this->getActivities();

        foreach ($activities as $activity) {
            $this->removeActivity($activity["ACT_UID"]);
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
        $data['DIA_UID'] = array_key_exists('DIA_UID', $data) ? $data['DIA_UID'] : Hash::generateUID();
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
        $data['PRO_UID'] = array_key_exists('PRO_UID', $data) ? $data['PRO_UID'] : Hash::generateUID();;
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
        $data['ACT_UID'] = array_key_exists('ACT_UID', $data) ? $data['ACT_UID'] : Hash::generateUID();;

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
        }

        return $activity;
    }

    public function getActivities($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        return Activity::getAll($this->getUid(), $start, $limit, $filter, $changeCaseTo);
    }

    public function updateActivity($actUid, $data)
    {
        try {
            self::log("Update Activity: $actUid", "With data: ", $data);

            $activity = ActivityPeer::retrieveByPk($actUid);

            // fixing data
            //$data['ELEMENT_UID'] = $data['BOU_ELEMENT_UID'];
            //unset($data['BOU_ELEMENT_UID']);

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

            self::log("Remove Activity Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function addEvent($data)
    {
        // setting defaults
        $data['EVN_UID'] = array_key_exists('EVN_UID', $data) ? $data['EVN_UID'] : Hash::generateUID();

        $event = new Event();
        $event->fromArray($data);
        $event->setPrjUid($this->project->getPrjUid());
        $event->setProUid($this->getProcess("object")->getProUid());
        $event->save();

        $this->events[$event->getEvnUid()] = $event;
    }

    public function getEvent($evnUid)
    {
        if (empty($this->events) || ! array_key_exists($evnUid, $this->activities)) {
            $event = EventPeer::retrieveByPK($evnUid);

            if (! is_object($event)) {
                return null;
            }

            $this->events[$evnUid] = $event;
        }

        return $this->events[$evnUid];
    }

    public function getEvents($retType = "array")
    {
        //return Event::getAll($this->project->getPrjUid(), null, null, '', 'object');
        return array();
    }

    public function addGateway($data)
    {
        // setting defaults
        $data['GAT_UID'] = array_key_exists('GAT_UID', $data) ? $data['GAT_UID'] : Hash::generateUID();

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
        }

        return $gateway;
    }

    public function getGateways($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        return  Gateway::getAll($this->getUid(), null, null, '', $changeCaseTo);
    }

    public function removeGateway($gatUid)
    {
        try {
            self::log("Remove Gateway: $gatUid");

            $gateway = GatewayPeer::retrieveByPK($gatUid);
            $gateway->delete();

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
        $data['FLO_UID'] = array_key_exists('FLO_UID', $data) ? $data['FLO_UID'] : Hash::generateUID();
        if (array_key_exists('FLO_STATE', $data)) {
            $data['FLO_STATE'] = is_array($data['FLO_STATE']) ? json_encode($data['FLO_STATE']) : $data['FLO_STATE'];
        }

        try {
            $flow = new Flow();
            $flow->fromArray($data, BasePeer::TYPE_FIELDNAME);
            $flow->setPrjUid($this->getUid());
            $flow->setDiaUid($this->getDiagram("object")->getDiaUid());
            $flow->save();

            self::log("Add Flow Success!");
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

        if ($retType != "object" && ! empty($activity)) {
            $flow = $flow->toArray();
        }

        return $flow;
    }

    public function getFlows($start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        if (is_array($start)) {
            extract($start);
        }

        return Flow::getAll($this->getUid(), null, null, '', $changeCaseTo);
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

    /*
     * Others functions/methods
     */

    public static function getDiffFromProjects($updatedProject)
    {
        // preparing target project
        $diagramElements = array(
            'act_uid' => 'activities',
            'evn_uid' => 'events',
            'flo_uid' => 'flows',
            'art_uid' => 'artifacts',
            'lns_uid' => 'laneset',
            'lan_uid' => 'lanes'
        );

        // Getting Differences
        $newRecords = array();
        $newRecordsUids = array();
        $deletedRecords = array();
        $updatedRecords = array();

        // Get new records
        foreach ($diagramElements as $key => $element) {
            if (! array_key_exists($element, $updatedProject['diagrams'][0])) {
                continue;
            }

            /*print_r($savedProject['diagrams'][0][$element]);
            print_r($updatedProject['diagrams'][0][$element]);
            var_dump($key);*/

            $arrayDiff = self::arrayDiff(
                $savedProject['diagrams'][0][$element],
                $updatedProject['diagrams'][0][$element],
                $key
            );

            if (! empty($arrayDiff)) {
                $newRecordsUids[$element] = $arrayDiff;

                foreach ($updatedProject['diagrams'][0][$element] as $item) {
                    if (in_array($item[$key], $newRecordsUids[$element])) {
                        $newRecords[$element][] = $item;
                    }
                }
            }
        }

        // Get deleted records
        foreach ($diagramElements as $key => $element) {
            if (! array_key_exists($element, $updatedProject['diagrams'][0])) {
                continue;
            }

            $arrayDiff = self::arrayDiff(
                $updatedProject['diagrams'][0][$element],
                $savedProject['diagrams'][0][$element],
                $key
            );

            if (! empty($arrayDiff)) {
                $deletedRecords[$element] = $arrayDiff;
            }
        }

        // Get updated records
        $checksum = array();
        foreach ($diagramElements as $key => $element) {
            $checksum[$element] = self::getArrayChecksum($savedProject['diagrams'][0][$element], $key);
        }


        foreach ($diagramElements as $key => $element) {
            if (! array_key_exists($element, $updatedProject['diagrams'][0])) {
                continue;
            }

            foreach ($updatedProject['diagrams'][0][$element] as $item) {
                if ((array_key_exists($element, $newRecordsUids) && in_array($item[$key], $newRecordsUids[$element])) ||
                    (array_key_exists($element, $deletedRecords) && in_array($item[$key], $deletedRecords[$element]))
                ) {
                    // skip new or deleted records
                    continue;
                }

                if (self::getChecksum($item) !== $checksum[$element][$item[$key]]) {
                    $updatedRecords[$element][] = $item;
                }
            }
        }

        $diff = array(
            'new' => $newRecords,
            'deleted' => $deletedRecords,
            'updated' => $updatedRecords
        );

        return $diff;
    }
}